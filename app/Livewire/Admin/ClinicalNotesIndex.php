<?php

namespace App\Livewire\Admin;

use App\Models\Appointment;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class ClinicalNotesIndex extends Component
{
    use WithPagination;

    public function render()
    {
        $clinicalNotes = Appointment::with(['service', 'services', 'doctor', 'patient'])
            ->whereNotNull('notes')
            ->where('status', 'completed')
            ->latest()
            ->paginate(15);

        return view('livewire.admin.clinical-notes-index', [
            'clinicalNotes' => $clinicalNotes,
        ]);
    }
}
