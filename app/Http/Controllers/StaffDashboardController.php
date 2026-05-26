<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Billing;
use App\Models\Patient;
use Illuminate\Http\Request;

class StaffDashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();

        $stats = [
            'today_count' => Appointment::whereDate('date', $today)->where('status', 'confirmed')->count(),
            'pending_count' => Appointment::where('status', 'pending')->count(),
            'total_count' => Appointment::count(),
            'unpaid_sum' => Billing::where('status', 'unpaid')->sum('amount'),
            'unpaid_count' => Billing::where('status', 'unpaid')->count(),
        ];

        $todayAppointments = Appointment::with(['patient', 'doctor', 'service', 'services'])
            ->whereDate('date', $today)
            ->where('status', 'confirmed')
            ->orderBy('time')
            ->get();

        $allAppointments = Appointment::with(['patient', 'doctor', 'service', 'services'])
            ->latest('date')
            ->paginate(15, pageName: 'appointments_page');

        $patients = Patient::withCount([
            'appointments as total_appointments',
            'appointments as confirmed_count' => fn ($q) => $q->where('status', 'confirmed'),
            'appointments as completed_count' => fn ($q) => $q->where('status', 'completed'),
        ])->orderBy('name')->paginate(15, pageName: 'patients_page');

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

    public function appointments(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor', 'service', 'services'])->latest('date');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('doctor', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('service', fn ($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        return response()->json($query->get());
    }

    public function cancelAppointment($id)
    {
        $appointment = Appointment::findOrFail($id);

        if (in_array($appointment->status, ['completed', 'canceled'], true)) {
            return back()->with('error', 'Cannot cancel a completed or already canceled appointment.');
        }

        $appointment->update(['status' => 'canceled']);

        return back()->with('success', 'Appointment has been canceled successfully.');
    }

    public function patients()
    {
        $patients = Patient::withCount([
            'appointments as total_appointments',
            'appointments as confirmed_count' => fn ($q) => $q->where('status', 'confirmed'),
            'appointments as completed_count' => fn ($q) => $q->where('status', 'completed'),
        ])->orderBy('name')->get();

        return response()->json($patients);
    }

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
