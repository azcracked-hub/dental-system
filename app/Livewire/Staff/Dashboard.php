<?php

namespace App\Livewire\Staff;

use App\Models\Appointment;
use App\Models\Billing;
use App\Models\Patient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.staff')]
class Dashboard extends Component
{
    use WithPagination;

    #[Url(as: 'tab')]
    public string $activeTab = 'today';

    #[Url]
    public string $search = '';

    public function mount(): void
    {
        $this->activeTab = in_array($this->activeTab, ['today', 'all', 'patients', 'billing'], true)
            ? $this->activeTab
            : 'today';
    }

    public function setActiveTab(string $tab): void
    {
        if (in_array($tab, ['today', 'all', 'patients', 'billing'], true)) {
            $this->activeTab = $tab;
        }
    }

    public function updatingSearch(): void
    {
        $this->activeTab = 'all';
        $this->resetPage('appointments_page');
    }

    public function cancelAppointment(int $id): void
    {
        $appointment = Appointment::findOrFail($id);

        if (in_array($appointment->status, ['completed', 'canceled'], true)) {
            session()->flash('error', 'Cannot cancel a completed or already canceled appointment.');

            return;
        }

        $appointment->update(['status' => 'canceled']);

        session()->flash('success', 'Appointment has been canceled successfully.');
    }

    public function render()
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

        $allAppointmentsQuery = Appointment::with(['patient', 'doctor', 'service', 'services'])
            ->latest('date');

        if ($this->search !== '') {
            $search = trim($this->search);
            $allAppointmentsQuery->where(function ($query) use ($search) {
                $query->whereHas('patient', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('doctor', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('service', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('services', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        $allAppointments = $allAppointmentsQuery->paginate(15, pageName: 'appointments_page');

        $patients = Patient::withCount([
            'appointments as total_appointments',
            'appointments as confirmed_count' => fn ($q) => $q->where('status', 'confirmed'),
            'appointments as completed_count' => fn ($q) => $q->where('status', 'completed'),
        ])->orderBy('name')->paginate(15, pageName: 'patients_page');

        $billings = Billing::with(['patient', 'appointment.service'])
            ->latest()
            ->get();

        return view('livewire.staff.dashboard', compact(
            'stats',
            'todayAppointments',
            'allAppointments',
            'patients',
            'billings'
        ));
    }
}
