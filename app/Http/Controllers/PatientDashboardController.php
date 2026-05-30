<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Billing;
use App\Models\Service;
use App\Models\User;
use App\Rules\NoDoubleBooking;
use App\Rules\ValidBookableDate;
use App\Rules\ValidBookableTime;
use App\Rules\ValidDoctorUser;
use App\Services\AppointmentService as AppointmentBookingService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientDashboardController extends Controller
{
    public function book()
    {
        return view('patient.appointments.book', [
            'services' => Service::orderBy('name')->get(),
            'doctors'  => User::doctors()->get(),
        ]);
    }

    public function store(Request $request, AppointmentBookingService $appointmentService, NotificationService $notifications)
    {
        $request->validate([
            'service_ids'   => 'required|array|min:1|max:3',
            'service_ids.*' => 'exists:services,id',
            'doctor_id'     => ['required', 'exists:users,id', new ValidDoctorUser],
            'date'          => ['required', 'date', 'after_or_equal:today', new ValidBookableDate, new NoDoubleBooking],
            'time'          => ['required', new ValidBookableTime],
        ], [
            'service_ids.required' => 'Please select at least one service.',
            'service_ids.max'      => 'You can select up to 3 services only.',
        ]);

        $patient = Auth::user()->resolvePatientRecord();

        $appointment = $appointmentService->create([
            'patients_id' => $patient->id,
            'doctor_id'   => $request->doctor_id,
            'date'        => $request->date,
            'time'        => $request->time,
            'status'      => 'pending',
        ], $request->service_ids, createBilling: true);

        $notifications->notifyAppointmentBooked($appointment);

        return redirect('/patient/dashboard')
            ->with('success', 'Appointment booked successfully!');
    }

    public function cancel(int $id)
    {
        $patientId = Auth::user()->resolvePatientRecord()->id;

        $appointment = Appointment::where('id', $id)
            ->where('patients_id', $patientId)
            ->firstOrFail();

        if ($appointment->status === 'completed') {
            return back()->with('error', 'Completed appointments cannot be canceled.');
        }

        if ($appointment->status === 'canceled') {
            return back()->with('error', 'Appointment is already canceled.');
        }

        $appointment->update(['status' => 'canceled']);

        return back()->with('success', 'Appointment canceled successfully.');
    }
}
