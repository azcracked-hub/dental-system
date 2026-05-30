<?php

namespace App\Livewire\Patient;

use App\Livewire\Concerns\WithAlerts;
use App\Models\Appointment;
use App\Models\Billing;
use App\Models\User;
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

        return view('livewire.patient.dashboard', compact(
            'nextAppointment',
            'appointments',
            'totalVisits',
            'pendingBalance',
            'unpaidBills',
            'clinicalNotes',
            'billings'
        ));
    }
}
