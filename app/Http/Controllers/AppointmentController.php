<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use App\Rules\NoDoubleBooking;
use App\Rules\ValidDoctorUser;
use App\Services\AppointmentService as AppointmentBookingService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['patient', 'service', 'services', 'doctor'])
            ->latest('date')
            ->paginate(15);

        $patients = Patient::orderBy('name')->get();
        $services = Service::orderBy('name')->get();

        return view('admin.appointments.index', compact(
            'appointments',
            'patients',
            'services'
        ));
    }

    public function store(Request $request, AppointmentBookingService $appointmentService)
    {
        $request->validate([
            'patients_id'   => 'required|exists:patients,id',
            'doctor_id'     => ['required', 'exists:users,id', new ValidDoctorUser],
            'service_ids'   => 'required|array|min:1|max:3',
            'service_ids.*' => 'exists:services,id',
            'date'          => ['required', 'date', new NoDoubleBooking],
            'time'          => 'required',
            'notes'         => 'nullable|string',
        ], [
            'service_ids.required' => 'Please select at least one service.',
            'service_ids.max'      => 'You can select up to 3 services only.',
        ]);

        $appointmentService->create([
            'patients_id' => $request->patients_id,
            'doctor_id'   => $request->doctor_id,
            'date'        => $request->date,
            'time'        => $request->time,
            'notes'       => $request->notes,
            'status'      => 'pending',
        ], $request->service_ids);

        return back()->with('success', 'Appointment created successfully.');
    }

    public function updateStatus(Request $request, int $id)
    {
        $appointment = Appointment::findOrFail($id);
        $request->validate(['status' => 'required|in:pending,confirmed,completed,canceled']);
        $appointment->update(['status' => $request->status]);

        return back()->with('success', 'Status updated.');
    }

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
