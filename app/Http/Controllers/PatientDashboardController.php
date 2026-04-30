<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Billing;
use App\Models\Service;
use App\Models\User;

class PatientDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $patientId = $user->patient->id;

        return view('dashboards.patient', [
            'nextAppointment' => Appointment::where('patients_id', $patientId)
                ->where('date', '>=', today())
                ->whereNotIn('status', ['canceled'])
                ->orderBy('date')
                ->orderBy('time')
                ->first(),

            'appointments' => Appointment::where('patients_id', $patientId)
                ->latest('date')
                ->get(),

            'totalVisits' => Appointment::where('patients_id', $patientId)
                ->where('status', 'completed')
                ->count(),

            'pendingBalance' => Billing::where('patients_id', $patientId)
                ->where('status', 'unpaid')
                ->sum('amount'),

            'unpaidBills' => Billing::where('patients_id', $patientId)
                ->where('status', 'unpaid')
                ->count(),

            'clinicalNotes' => Appointment::where('patients_id', $patientId)
                ->whereNotNull('notes')
                ->where('status', 'completed')
                ->latest()
                ->get(),

            'billings' => Billing::where('patients_id', $patientId)
                ->latest()
                ->get(),
        ]);
    }
    public function book()
    {
        return view('patient.appointments.book', [
            'services' => Service::all(),
            'doctors'  => User::whereIn('role', ['admin', 'doctor'])->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'doctor_id'  => 'required|exists:users,id',
            'date'       => 'required|date|after_or_equal:today',
            'time'       => 'required',
        ]);

        $service = Service::findOrFail($request->service_id);

        $appointment = Appointment::create([
            'patients_id' => Auth::user()->patient->id,
            'doctor_id'   => $request->doctor_id,
            'service_id'  => $service->id,
            'date'        => $request->date,
            'time'        => $request->time,
            'status'      => 'pending',
        ]);

        // AUTO CREATE BILLING (minimal addition)
        Billing::create([
            'appointment_id' => $appointment->id,
            'patients_id' => Auth::user()->patient->id,
            'amount'         => $service->price,
            'status'         => 'unpaid',
            'description'    => $service->name,
        ]);

        return redirect('/patient/dashboard')
            ->with('success', 'Appointment booked successfully!');
    }

    public function cancel(int $id)
    {
        $patientId = Auth::user()->patient->id;

        $appointment = Appointment::where('id', $id)
            ->where('patients_id', $patientId)
            ->firstOrFail();

        if ($appointment->status === 'completed') {
            return back()->with('error', 'Completed appointments cannot be cancelled.');
        }

        if ($appointment->status === 'canceled') {
            return back()->with('error', 'Appointment is already cancelled.');
        }

        $appointment->update([
            'status' => 'canceled'
        ]);

        return back()->with('success', 'Appointment cancelled successfully.');
    }
}
