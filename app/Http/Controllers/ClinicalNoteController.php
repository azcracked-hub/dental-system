<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;

class ClinicalNoteController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $clinicalNotes = Appointment::with(['service', 'doctor', 'patient'])
            ->where('patients_id', $user->id)
            ->whereNotNull('notes')
            ->where('status', 'completed')
            ->latest()
            ->get();

        return view('admin.clinical-notes.index', compact('clinicalNotes'));
    }
}
