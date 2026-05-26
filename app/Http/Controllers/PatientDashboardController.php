<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Billing;
use App\Models\Service;
use App\Models\User;
use App\Rules\NoDoubleBooking;
use App\Rules\ValidDoctorUser;
use App\Services\AppointmentService as AppointmentBookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientDashboardController extends Controller
{
    public function index()
    {
        $patient = Auth::user()->resolvePatientRecord();
        $patientId = $patient->id;

        $withServices = fn ($query) => $query->with(['service', 'services', 'doctor']);

        return view('dashboards.patient', [
            'nextAppointment' => $withServices(
                Appointment::where('patients_id', $patientId)
                    ->where('date', '>=', today())
                    ->whereNotIn('status', ['canceled'])
                    ->orderBy('date')
                    ->orderBy('time')
            )->first(),

            'appointments' => $withServices(
                Appointment::where('patients_id', $patientId)->latest('date')
            )->paginate(10),

            'totalVisits' => Appointment::where('patients_id', $patientId)
                ->where('status', 'completed')
                ->count(),

            'pendingBalance' => Billing::where('patients_id', $patientId)
                ->where('status', 'unpaid')
                ->sum('amount'),

            'unpaidBills' => Billing::where('patients_id', $patientId)
                ->where('status', 'unpaid')
                ->count(),

            'clinicalNotes' => $withServices(
                Appointment::where('patients_id', $patientId)
                    ->whereNotNull('notes')
                    ->where('status', 'completed')
                    ->latest()
            )->get(),

            'billings' => Billing::where('patients_id', $patientId)
                ->latest()
                ->get(),
        ]);
    }

    public function book()
    {
        return view('patient.appointments.book', [
            'services' => Service::orderBy('name')->get(),
            'doctors'  => User::doctors()->get(),
        ]);
    }

    public function store(Request $request, AppointmentBookingService $appointmentService)
    {
        $request->validate([
            'service_ids'   => 'required|array|min:1|max:3',
            'service_ids.*' => 'exists:services,id',
            'doctor_id'     => ['required', 'exists:users,id', new ValidDoctorUser],
            'date'          => ['required', 'date', 'after_or_equal:today', new NoDoubleBooking],
            'time'          => 'required',
        ], [
            'service_ids.required' => 'Please select at least one service.',
            'service_ids.max'      => 'You can select up to 3 services only.',
        ]);

        $patient = Auth::user()->resolvePatientRecord();

        $appointmentService->create([
            'patients_id' => $patient->id,
            'doctor_id'   => $request->doctor_id,
            'date'        => $request->date,
            'time'        => $request->time,
            'status'      => 'pending',
        ], $request->service_ids, createBilling: true);

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
