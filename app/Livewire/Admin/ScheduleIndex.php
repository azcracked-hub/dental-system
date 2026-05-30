<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithAlerts;
use App\Models\ClinicHoliday;
use App\Models\DoctorUnavailability;
use App\Models\User;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class ScheduleIndex extends Component
{
    use WithAlerts;

    public string $holidayDate = '';
    public string $holidayName = '';
    public string $unavailDoctorId = '';
    public string $unavailDate = '';
    public string $unavailReason = '';

    public function addHoliday(): void
    {
        $validated = $this->validate([
            'holidayDate' => 'required|date|unique:clinic_holidays,date',
            'holidayName' => 'required|string|max:255',
        ], [], [
            'holidayDate' => 'date',
            'holidayName' => 'holiday name',
        ]);

        ClinicHoliday::create([
            'date' => $validated['holidayDate'],
            'name' => $validated['holidayName'],
        ]);

        $this->reset(['holidayDate', 'holidayName']);
        $this->alertSuccess('Holiday added. Patients cannot book on this date.');
    }

    public function removeHoliday(int $id): void
    {
        ClinicHoliday::findOrFail($id)->delete();
        $this->alertSuccess('Holiday removed.');
    }

    public function addUnavailability(NotificationService $notifications): void
    {
        $validated = $this->validate([
            'unavailDoctorId' => 'required|exists:users,id',
            'unavailDate' => 'required|date|after_or_equal:today',
            'unavailReason' => 'nullable|string|max:255',
        ], [], [
            'unavailDoctorId' => 'doctor',
            'unavailDate' => 'date',
        ]);

        $existing = DoctorUnavailability::where('doctor_id', $validated['unavailDoctorId'])
            ->whereDate('date', $validated['unavailDate'])
            ->first();

        if ($existing) {
            $this->alertWarning('Doctor is already marked unavailable on this date.');
            return;
        }

        DoctorUnavailability::create([
            'doctor_id' => $validated['unavailDoctorId'],
            'date' => $validated['unavailDate'],
            'reason' => $validated['unavailReason'] ?: null,
        ]);

        $count = $notifications->markAffectedAppointmentsForReschedule(
            (int) $validated['unavailDoctorId'],
            $validated['unavailDate'],
            $validated['unavailReason'] ?: 'Doctor unavailable'
        );

        $this->reset(['unavailDate', 'unavailReason']);
        $message = 'Unavailable date saved.';
        if ($count > 0) {
            $message .= " {$count} appointment(s) flagged for rescheduling and patients notified.";
        }
        $this->alertSuccess($message);
    }

    public function removeUnavailability(int $id): void
    {
        DoctorUnavailability::findOrFail($id)->delete();
        $this->alertSuccess('Unavailable date removed.');
    }

    public function render()
    {
        return view('livewire.admin.schedule-index', [
            'holidays' => ClinicHoliday::orderBy('date')->get(),
            'unavailabilities' => DoctorUnavailability::with('doctor')->orderBy('date')->get(),
            'doctors' => User::doctors()->get(),
        ]);
    }
}
