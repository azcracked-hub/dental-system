<?php

namespace App\Livewire\Admin;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class SystemAdminIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.system-admin-index', [
            'users' => User::orderBy('name')->get(),
            'totalPatients' => Patient::count(),
            'totalAppointments' => Appointment::count(),
        ]);
    }
}
