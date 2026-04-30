<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Billing;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with('patient')->latest('date')->get();
        $patients     = Patient::orderBy('name')->get();

        return view('admin.appointments.index', compact('appointments', 'patients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patients_id' => 'required|exists:patients,id',
            'doctor_id'   => 'required|exists:users,id',
            'service_id'  => 'required|exists:services,id',
            'date'        => 'required|date',
            'time'        => 'required',
            'notes'       => 'nullable|string',
        ]);

        Appointment::create([
            'patients_id' => $request->patients_id,
            'doctor_id'   => $request->doctor_id,
            'service_id'  => $request->service_id,
            'date'        => $request->date,
            'time'        => $request->time,
            'status'      => 'pending',
            'notes'       => $request->notes,
        ]);

        return back()->with('success', 'Appointment created successfully.');
    }

    public function updateStatus(Request $request,int $id)
    {
        $appointment = Appointment::findOrFail($id);
        $request->validate(['status' => 'required|in:pending,confirmed,completed,canceled']);
        $appointment->update(['status' => $request->status]);

        return back()->with('success', 'Status updated.');
    }

    // Called from "Complete & Add Notes" modal
    public function complete(Request $request, int $id)
    {
        $request->validate(['notes' => 'required|string']);

        $appointment = Appointment::findOrFail($id);
        $appointment->update([
            'status' => 'completed',
            'notes'  => $request->notes,
        ]);

        return back()->with('success', 'Appointment completed and notes saved.');
    }

    public function destroy(int $id)
    {
        Appointment::findOrFail($id)->delete();
        return back()->with('success', 'Appointment deleted.');
    }
}
