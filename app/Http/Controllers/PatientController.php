<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::withCount('appointments')->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $patients = $query->paginate(15)->withQueryString();

        return view('admin.patients.index', compact('patients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:patients,email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
        ], [
            'email.unique' => 'This email is already registered.',
            'password' => 'Password must be at least 8 characters and include uppercase, lowercase, and a number (e.g. Password1).',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'phone'    => $request->phone,
                'password' => $request->password,
                'role'     => 'patient',
            ]);

            Patient::create([
                'user_id' => $user->id,
                'name'    => $request->name,
                'email'   => $request->email,
                'phone'   => $request->phone,
                'address' => $request->address,
            ]);
        });

        return back()->with('success', 'Patient added successfully.');
    }

    public function show(Patient $patient)
    {
        $patient->load(['appointments.service', 'appointments.services', 'billings.appointment']);

        return view('admin.patients.show', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => [
                'required',
                'email',
                Rule::unique('patients', 'email')->ignore($patient->id),
                Rule::unique('users', 'email')->ignore($patient->user_id),
            ],
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ], [
            'email.unique' => 'This email is already registered to another account.',
        ]);

        DB::transaction(function () use ($request, $patient) {
            $patient->update($request->only('name', 'email', 'phone', 'address'));

            if ($patient->user) {
                $patient->user->update($request->only('name', 'email', 'phone'));
            }
        });

        return back()->with('success', 'Patient updated.');
    }

    public function destroy(Patient $patient)
    {
        DB::transaction(function () use ($patient) {
            $user = $patient->user;
            $patient->delete();
            $user?->delete();
        });

        return back()->with('success', 'Patient deleted.');
    }
}
