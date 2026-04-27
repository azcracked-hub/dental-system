<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::withCount('appointments')->orderBy('name')->get();
        return view('patients.index', compact('patients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:patients,email',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        Patient::create($request->only('name', 'email', 'phone', 'address'));
        return back()->with('success', 'Patient added successfully.');
    }

    public function show($id)
    {
        $patient = Patient::with(['appointments', 'billings.appointment'])->findOrFail($id);
        return view('patients.show', compact('patient'));
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:patients,email,' . $id,
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $patient->update($request->only('name', 'email', 'phone', 'address'));
        return back()->with('success', 'Patient updated.');
    }

    public function destroy($id)
    {
        Patient::findOrFail($id)->delete();
        return back()->with('success', 'Patient deleted.');
    }
}
