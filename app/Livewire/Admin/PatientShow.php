<?php

namespace App\Livewire\Admin;

use App\Models\Patient;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class PatientShow extends Component
{
    public Patient $patient;
    public bool $showEditModal = false;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';

    public function mount(Patient $patient): void
    {
        $this->patient = $patient->load([
            'appointments.service',
            'appointments.services',
            'billings.appointment.service',
            'user',
        ]);

        $this->fillPatientForm();
    }

    public function openEditModal(): void
    {
        $this->resetValidation();
        $this->fillPatientForm();
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
    }

    public function updatePatient(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email,' . $this->patient->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $this->patient->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?: null,
            'address' => $validated['address'] ?: null,
        ]);

        if ($this->patient->user) {
            $this->patient->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?: null,
            ]);
        }

        $this->patient->refresh();
        $this->fillPatientForm();
        $this->showEditModal = false;
        session()->flash('success', 'Patient updated.');
    }

    private function fillPatientForm(): void
    {
        $this->name = (string) $this->patient->name;
        $this->email = (string) $this->patient->email;
        $this->phone = (string) ($this->patient->phone ?? '');
        $this->address = (string) ($this->patient->address ?? '');
    }

    public function render()
    {
        $this->patient->load([
            'appointments.service',
            'appointments.services',
            'billings.appointment.service',
        ]);

        return view('livewire.admin.patient-show', [
            'patient' => $this->patient,
        ]);
    }
}
