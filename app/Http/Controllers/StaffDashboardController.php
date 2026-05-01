<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Billing;
use App\Models\Patient;
use Illuminate\Http\Request;

class StaffDashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD (UI)
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $today = now()->toDateString();

        $stats = [
            'today_count' => Appointment::whereDate('date', $today)->where('status', 'confirmed')->count(),
            'pending_count' => Appointment::where('status', 'pending')->count(),
            'total_count' => Appointment::count(),
            'unpaid_sum' => Billing::where('status', 'unpaid')->sum('amount'),
            'unpaid_count' => Billing::where('status', 'unpaid')->count(),
        ];

        $todayAppointments = Appointment::with(['patient', 'doctor', 'service'])
            ->whereDate('date', $today)
            ->where('status', 'confirmed')
            ->orderBy('time')
            ->get();

        $allAppointments = Appointment::with(['patient', 'doctor', 'service'])
            ->latest('date')
            ->get();

        $patients = Patient::withCount([
            'appointments as total_appointments',
            'appointments as confirmed_count' => fn($q) => $q->where('status', 'confirmed'),
            'appointments as completed_count' => fn($q) => $q->where('status', 'completed')
        ])->orderBy('name')->get();

        $billings = Billing::with(['patient', 'appointment.service'])
            ->latest()
            ->get();

        return view('dashboards.staff', compact(
            'stats',
            'todayAppointments',
            'allAppointments',
            'patients',
            'billings'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | APPOINTMENTS (API / AJAX)
    |--------------------------------------------------------------------------
    */
    public function appointments(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor', 'service'])->latest('date');

        if ($request->filled('search')) {
            $search = $request->search;

            // FIXED: grouped search logic
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('doctor', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('service', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        return response()->json($query->get());
    }

    public function cancelAppointment($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->status === 'completed') {
            return back()->with('error', 'Completed appointments cannot be cancelled.');
}
        if (in_array($appointment->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Cannot cancel a completed or already cancelled appointment.');
        }


        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Appointment has been cancelled successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | PATIENTS (READ ONLY)
    |--------------------------------------------------------------------------
    */
    public function patients()
    {
        $patients = Patient::withCount([
            'appointments as total_appointments',
            'appointments as confirmed_count' => fn($q) => $q->where('status', 'confirmed'),
            'appointments as completed_count' => fn($q) => $q->where('status', 'completed')
        ])->orderBy('name')->get();

        return response()->json($patients);
    }

    /*
    |--------------------------------------------------------------------------
    | BILLING (READ ONLY)
    |--------------------------------------------------------------------------
    */
    public function billing()
    {
        $billings = Billing::with(['patient', 'appointment.service'])
            ->latest()
            ->get();

        return response()->json($billings);
    }

    public function storeBilling()
    {
        abort(403, 'Staff members cannot modify billing amounts.');
    }
}
