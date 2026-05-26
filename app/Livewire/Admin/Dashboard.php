<?php

namespace App\Livewire\Admin;

use App\Models\Appointment;
use App\Models\Billing;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Dashboard extends Component
{
    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="p-6 text-sm text-gray-500">Loading dashboard...</div>
        HTML;
    }

    public function render(): View
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

        return view('livewire.admin.dashboard', compact(
            'confirmed', 'pending', 'completed', 'canceled',
            'todayCount', 'totalRevenue', 'pendingPayments',
            'todayAppointments', 'recentNotes'
        ));
    }
}
