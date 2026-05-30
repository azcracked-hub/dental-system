<?php

namespace App\Livewire\Patient;

use App\Livewire\Concerns\WithAlerts;
use App\Models\Appointment;
use App\Models\Billing;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\SchedulingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.patient')]
class Dashboard extends Component
{
    use WithAlerts;
    use WithPagination;

    #[Url(as: 'tab')]
    public string $activeTab = 'appointments';

    public bool $showViewModal = false;
    public bool $showRescheduleModal = false;
    public ?int $viewAppointmentId = null;
    public ?int $rescheduleAppointmentId = null;
    public string $rescheduleDate = '';
    public string $rescheduleTime = '';
    /** @var array<string, string> */
    public array $rescheduleSlotOptions = [];

    public function updatedRescheduleDate(SchedulingService $scheduling): void
    {
        $this->rescheduleTime = '';
        $this->rescheduleSlotOptions = [];

        if (! $this->rescheduleAppointmentId || ! $this->rescheduleDate) {
            return;
        }

        $appointment = Appointment::find($this->rescheduleAppointmentId);
        if (! $appointment) {
            return;
        }

        $reason = $scheduling->getDayBlockReason((int) $appointment->doctor_id, $this->rescheduleDate);
        if ($reason) {
            $this->addError('rescheduleDate', $reason);

            return;
        }

        $this->resetErrorBag('rescheduleDate');
        $this->rescheduleSlotOptions = $scheduling->getAvailableSlots((int) $appointment->doctor_id, $this->rescheduleDate);
    }

    public function mount(): void
    {
        $this->activeTab = in_array($this->activeTab, ['appointments', 'notes', 'billing'], true)
            ? $this->activeTab
            : 'appointments';
    }

    public function setActiveTab(string $tab): void
    {
        if (in_array($tab, ['appointments', 'notes', 'billing'], true)) {
            $this->activeTab = $tab;
        }
    }

    public function cancelAppointment(int $id): void
    {
        /** @var User $user */
        $user = Auth::user();
        $patientId = $user->resolvePatientRecord()->id;

        $appointment = Appointment::where('id', $id)
            ->where('patients_id', $patientId)
            ->firstOrFail();

        if ($appointment->status === 'completed') {
            $this->alertError('Completed appointments cannot be canceled.');

            return;
        }

        if ($appointment->status === 'canceled') {
            $this->alertError('Appointment is already canceled.');

            return;
        }

        $appointment->update(['status' => 'canceled']);

        $this->alertSuccess('Appointment canceled successfully.');
    }

    public function viewAppointment(int $id): void
    {
        $patientId = Auth::user()->resolvePatientRecord()->id;
        Appointment::where('id', $id)->where('patients_id', $patientId)->firstOrFail();
        $this->viewAppointmentId = $id;
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewAppointmentId = null;
    }

    public function openReschedule(int $id): void
    {
        $patientId = Auth::user()->resolvePatientRecord()->id;
        $appointment = Appointment::where('id', $id)->where('patients_id', $patientId)->firstOrFail();
        $this->rescheduleAppointmentId = $appointment->id;
        $this->rescheduleDate = '';
        $this->rescheduleTime = '';
        $this->rescheduleSlotOptions = [];
        $this->showRescheduleModal = true;
        $this->closeViewModal();
    }

    public function closeRescheduleModal(): void
    {
        $this->showRescheduleModal = false;
        $this->reset(['rescheduleAppointmentId', 'rescheduleDate', 'rescheduleTime', 'rescheduleSlotOptions']);
    }

    public function rescheduleAppointment(NotificationService $notifications, SchedulingService $scheduling): void
    {
        $patientId = Auth::user()->resolvePatientRecord()->id;
        $appointment = Appointment::where('id', $this->rescheduleAppointmentId)
            ->where('patients_id', $patientId)
            ->firstOrFail();

        if (! in_array($appointment->status, ['needs_reschedule', 'pending', 'confirmed'], true)) {
            $this->alertError('This appointment cannot be rescheduled.');
            return;
        }

        $this->validate([
            'rescheduleDate' => 'required|date|after_or_equal:today',
            'rescheduleTime' => 'required',
        ]);

        $reason = $scheduling->getDayBlockReason((int) $appointment->doctor_id, $this->rescheduleDate);
        if ($reason) {
            $this->addError('rescheduleDate', $reason);
            return;
        }

        if (! $scheduling->isSlotAvailable((int) $appointment->doctor_id, $this->rescheduleDate, $this->rescheduleTime)) {
            $this->addError('rescheduleTime', 'The selected time is not available.');
            return;
        }

        $normalizedTime = substr($this->rescheduleTime, 0, 5);
        $conflict = Appointment::query()
            ->where('doctor_id', $appointment->doctor_id)
            ->whereDate('date', $this->rescheduleDate)
            ->where('id', '!=', $appointment->id)
            ->whereNotIn('status', ['canceled'])
            ->where(function ($q) use ($normalizedTime) {
                $q->where('time', 'like', $normalizedTime.'%');
            })
            ->exists();

        if ($conflict) {
            $this->addError('rescheduleTime', 'This doctor already has an appointment at the selected time.');
            return;
        }

        $appointment->update([
            'date' => $this->rescheduleDate,
            'time' => $this->rescheduleTime,
            'status' => 'pending',
        ]);

        $appointment->load('doctor');

        if ($appointment->doctor) {
            $notifications->notify($appointment->doctor, 'Appointment rescheduled', 'A patient rescheduled to '.$this->rescheduleDate.'.', 'info', ['appointment_id' => $appointment->id]);
        }

        $this->alertSuccess('Appointment rescheduled successfully.');
        $this->closeRescheduleModal();
    }

    public function render()
    {
        /** @var User $user */
        $user = Auth::user();
        $patient = $user->resolvePatientRecord();
        $patientId = $patient->id;

        $withServices = fn ($query) => $query->with(['service', 'services', 'doctor']);

        $nextAppointment = $withServices(
            Appointment::where('patients_id', $patientId)
                ->where('date', '>=', today())
                ->whereNotIn('status', ['canceled'])
                ->orderBy('date')
                ->orderBy('time')
        )->first();

        $appointments = $withServices(
            Appointment::where('patients_id', $patientId)->latest('date')
        )->paginate(10);

        $totalVisits = Appointment::where('patients_id', $patientId)
            ->where('status', 'completed')
            ->count();

        $pendingBalance = Billing::where('patients_id', $patientId)
            ->where('status', 'unpaid')
            ->sum('amount');

        $unpaidBills = Billing::where('patients_id', $patientId)
            ->where('status', 'unpaid')
            ->count();

        $clinicalNotes = $withServices(
            Appointment::where('patients_id', $patientId)
                ->whereNotNull('notes')
                ->where('status', 'completed')
                ->latest()
        )->get();

        $billings = Billing::where('patients_id', $patientId)
            ->latest()
            ->get();

        $viewAppointment = $this->viewAppointmentId
            ? $withServices(Appointment::where('id', $this->viewAppointmentId)->where('patients_id', $patientId))->first()
            : null;

        return view('livewire.patient.dashboard', compact(
            'nextAppointment',
            'appointments',
            'totalVisits',
            'pendingBalance',
            'unpaidBills',
            'clinicalNotes',
            'billings',
            'viewAppointment'
        ));
    }
}
