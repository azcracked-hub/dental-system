<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;

class SystemAdminController extends Controller
{
    public function index()
    {
        $users             = User::orderBy('name')->get();
        $totalPatients     = Patient::count();
        $totalAppointments = Appointment::count();

        return view('admin.system-admin.index', compact('users', 'totalPatients', 'totalAppointments'));
    }
}
