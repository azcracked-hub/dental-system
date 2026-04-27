<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Billing;

class AdminController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $todayAppointments = Appointment::with('patient')
            ->whereDate('date', $today)
            ->orderBy('time')
            ->get();

        $confirmed = Appointment::where('status', 'confirmed')->count();
        $pending   = Appointment::where('status', 'pending')->count();
        $completed = Appointment::where('status', 'completed')->count();
        $canceled  = Appointment::where('status', 'canceled')->count();

        $todayCount      = $todayAppointments->count();
        $totalRevenue    = Billing::where('status', 'paid')->sum('amount');
        $pendingPayments = Billing::where('status', 'unpaid')->sum('amount');

        $recentNotes = Appointment::with('patient')
            ->whereNotNull('notes')
            ->where('status', 'completed')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboards.admin', compact(
            'confirmed', 'pending', 'completed', 'canceled',
            'todayCount', 'totalRevenue', 'pendingPayments',
            'todayAppointments', 'recentNotes'
        ));
    }
}
