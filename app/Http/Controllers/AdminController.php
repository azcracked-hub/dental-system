<?php

namespace App\Http\Controllers;
use App\Models\Appointment;
use App\Models\Billing;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function appointment()
    {
        $today = now()->toDateString();
    
        $todayAppointments = Appointment::whereDate('date', $today)->get();
    
        $confirmed = Appointment::where('status', 'confirmed')->count();
        $pending = Appointment::where('status', 'pending')->count();
        $completed = Appointment::where('status', 'completed')->count();
        $canceled = Appointment::where('status', 'canceled')->count();
    
        $todayCount = $todayAppointments->count();
    
        // Billing (optional if you already have table)
        //$totalRevenue = Billing::sum('amount') ?? 0;
        //$pendingPayments = Billing::where('status', 'unpaid')->sum('amount') ?? 0;
    
        // Recent clinical notes (from appointments notes)
        $recentNotes = Appointment::whereNotNull('notes')
            ->latest()
            ->take(5)
            ->get();
    
        return view('dashboard.admin', compact(
            'confirmed',
            'pending',
            'completed',
            'canceled',
            'todayCount',
            'totalRevenue',
            'pendingPayments',
            'todayAppointments',
            'recentNotes'
        ));
    } 
}
