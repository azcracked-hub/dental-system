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

        return view('system-admin.index', compact('users', 'totalPatients', 'totalAppointments'));
    }
    public function toggleRole($id)
{
    $user = User::findOrFail($id);
    $user->update([
        'role' => $user->role === 'admin' ? 'patient' : 'admin'
    ]);

    return back()->with('success', 'User role updated successfully.');
}
}
