<?php

namespace App\Livewire\Admin;

use App\Models\Appointment;
use App\Models\Billing;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use App\Rules\NoDoubleBooking;
use App\Rules\ValidDoctorUser;
use App\Services\AppointmentService as AppointmentBookingService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class AppointmentsIndex extends Component
{
    use WithPagination;

    public bool $showCreateModal = false;
    public bool $showCompleteModal = false;
    public bool $showBillingModal = false;

    public string $patients_id = '';
    public string $doctor_id = '';
    public string $date = '';
    public string $time = '';
    public string $notes = '';
    public array $service_ids = [];

    public ?int $completeAppointmentId = null;
    public string $completeNotes = '';
    public ?int $billingAppointmentId = null;
    public ?float $billingAmount = null;

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    public function resetCreateForm(): void
    {
        $this->reset(['patients_id', 'doctor_id', 'date', 'time', 'notes', 'service_ids']);
    }

    public function toggleService(int $id): void
    {
        if (in_array($id, $this->service_ids, true)) {
            $this->service_ids = array_values(array_filter(
                $this->service_ids,
                fn (int $serviceId): bool => $serviceId !== $id
            ));
            return;
        }

        if (count($this->service_ids) >= 3) {
            $this->addError('service_ids', 'You can select up to 3 services only.');
            return;
        }

        $this->service_ids[] = $id;
    }

    public function createAppointment(AppointmentBookingService $appointmentService): void
    {
        $validated = $this->validate([
            'patients_id' => 'required|exists:patients,id',
            'doctor_id' => ['required', 'exists:users,id', new ValidDoctorUser],
            'service_ids' => 'required|array|min:1|max:3',
            'service_ids.*' => 'exists:services,id',
            'date' => ['required', 'date', new NoDoubleBooking],
            'time' => 'required',
            'notes' => 'nullable|string',
        ], [
            'service_ids.required' => 'Please select at least one service.',
            'service_ids.max' => 'You can select up to 3 services only.',
        ]);

        $appointmentService->create([
            'patients_id' => $validated['patients_id'],
            'doctor_id' => $validated['doctor_id'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'notes' => $validated['notes'] ?: null,
            'status' => 'pending',
        ], $validated['service_ids']);

        $this->showCreateModal = false;
        $this->resetCreateForm();
        session()->flash('success', 'Appointment created successfully.');
    }

    public function openCompleteModal(int $appointmentId): void
    {
        $appointment = Appointment::with('patient')->findOrFail($appointmentId);
        $this->resetValidation();
        $this->completeAppointmentId = $appointment->id;
        $this->completeNotes = $appointment->notes ?? '';
        $this->showCompleteModal = true;
    }

    public function closeCompleteModal(): void
    {
        $this->showCompleteModal = false;
        $this->reset(['completeAppointmentId', 'completeNotes']);
    }

    public function completeAppointment(): void
    {
        $validated = $this->validate([
            'completeAppointmentId' => 'required|exists:appointments,id',
            'completeNotes' => 'required|string',
        ]);

        $appointment = Appointment::findOrFail($validated['completeAppointmentId']);
        if (in_array($appointment->status, ['completed', 'canceled'], true)) {
            $this->closeCompleteModal();
            session()->flash('error', 'Only active appointments can be completed.');
            return;
        }

        $appointment->update([
            'status' => 'completed',
            'notes' => $validated['completeNotes'],
        ]);

        $this->closeCompleteModal();
        session()->flash('success', 'Appointment completed and notes saved.');
    }

    public function openBillingModal(int $appointmentId): void
    {
        $appointment = Appointment::with(['services', 'service'])->findOrFail($appointmentId);
        $this->resetValidation();
        $this->billingAppointmentId = $appointment->id;
        $this->billingAmount = $appointment->totalServicePrice();
        $this->showBillingModal = true;
    }

    public function closeBillingModal(): void
    {
        $this->showBillingModal = false;
        $this->reset(['billingAppointmentId', 'billingAmount']);
    }

    public function createBilling(): void
    {
        $validated = $this->validate([
            'billingAppointmentId' => 'required|exists:appointments,id',
            'billingAmount' => 'nullable|numeric|min:0',
        ]);

        $existing = Billing::where('appointment_id', $validated['billingAppointmentId'])->first();
        if ($existing) {
            $this->closeBillingModal();
            session()->flash('success', 'Billing already exists for this appointment.');
            return;
        }

        $appointment = Appointment::with(['patient', 'service', 'services'])
            ->findOrFail($validated['billingAppointmentId']);
        if ($appointment->status === 'canceled') {
            $this->closeBillingModal();
            session()->flash('error', 'Cannot create billing for canceled appointments.');
            return;
        }

        Billing::create([
            'appointment_id' => $appointment->id,
            'patients_id' => $appointment->patients_id,
            'amount' => $validated['billingAmount'] ?? $appointment->service?->price ?? 0,
            'status' => 'unpaid',
            'description' => $appointment->serviceNames(),
        ]);

        $this->closeBillingModal();
        session()->flash('success', 'Billing record created.');
    }

    public function render()
    {
        $appointments = Appointment::with(['patient', 'service', 'services', 'doctor'])
            ->latest('date')
            ->paginate(15);

        return view('livewire.admin.appointments-index', [
            'appointments' => $appointments,
            'patients' => Patient::orderBy('name')->get(),
            'services' => Service::orderBy('name')->get(),
            'doctors' => User::doctors()->get(),
        ]);
    }
}
