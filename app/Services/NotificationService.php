<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Models\UserNotification;

class NotificationService
{
    public function notify(User $user, string $title, string $message, string $type = 'info', array $data = []): UserNotification
    {
        return UserNotification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data,
        ]);
    }

    public function notifyPatientForAppointment(Patient $patient, Appointment $appointment, string $title, string $message, string $type = 'info'): void
    {
        $user = $patient->user;
        if (! $user) {
            return;
        }

        $this->notify($user, $title, $message, $type, [
            'appointment_id' => $appointment->id,
        ]);
    }

    public function notifyRescheduleRequired(Appointment $appointment, string $reason): void
    {
        $appointment->load('patient.user');
        $patient = $appointment->patient;
        if (! $patient) {
            return;
        }

        $this->notifyPatientForAppointment(
            $patient,
            $appointment,
            'Reschedule required',
            "Your appointment on {$appointment->date} needs to be rescheduled. Reason: {$reason}",
            'warning'
        );
    }

    public function notifyAppointmentBooked(Appointment $appointment): void
    {
        $appointment->load(['patient.user', 'doctor']);
        if ($appointment->patient) {
            $this->notifyPatientForAppointment(
                $appointment->patient,
                $appointment,
                'Appointment booked',
                'Your appointment request has been submitted and is pending confirmation.',
                'success'
            );
        }

        if ($appointment->doctor) {
            $this->notify(
                $appointment->doctor,
                'New appointment request',
                'A patient booked an appointment on '.$appointment->date.'.',
                'info',
                ['appointment_id' => $appointment->id]
            );
        }
    }

    public function markAffectedAppointmentsForReschedule(int $doctorId, string $date, string $reason): int
    {
        $appointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();

        foreach ($appointments as $appointment) {
            $appointment->update(['status' => 'needs_reschedule']);
            $this->notifyRescheduleRequired($appointment, $reason);
        }

        return $appointments->count();
    }
}
