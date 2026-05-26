<?php

namespace App\Http\Controllers;

use App\Models\Appointment;

class ClinicalNoteController extends Controller
{
    public function index()
    {
        $clinicalNotes = Appointment::with(['service', 'services', 'doctor', 'patient'])
            ->whereNotNull('notes')
            ->where('status', 'completed')
            ->latest()
            ->get();

        return view('admin.clinical-notes.index', compact('clinicalNotes'));
    }
}
