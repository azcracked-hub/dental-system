<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithAlerts;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class PatientsIndex extends Component
{
    use WithAlerts;
    use WithPagination;

    #[Url]
    public string $search = '';

    public bool $showCreateModal = false;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $password = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    public function store(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
        ], [
            'email.unique' => 'This email is already registered.',
            'password' => 'Password must be at least 8 characters and include uppercase, lowercase, and a number (e.g. Password1).',
        ]);

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?: null,
                'password' => $validated['password'],
                'role' => 'patient',
            ]);

            Patient::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?: null,
                'address' => $validated['address'] ?: null,
            ]);
        });

        $this->reset(['name', 'email', 'phone', 'address', 'password']);
        $this->showCreateModal = false;
        $this->resetPage();
        $this->alertSuccess('Patient added successfully.');
    }

    public function deletePatient(int $id): void
    {
        $patient = Patient::with('user')->findOrFail($id);

        DB::transaction(function () use ($patient): void {
            $user = $patient->user;
            $patient->delete();
            $user?->delete();
        });

        $this->alertSuccess('Patient deleted.');
        $this->resetPage();
    }

    public function render()
    {
        $patients = Patient::query()
            ->withCount('appointments')
            ->orderBy('name')
            ->when($this->search !== '', function ($query): void {
                $search = $this->search;
                $query->where(function ($inner) use ($search): void {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->paginate(15);

        return view('livewire.admin.patients-index', [
            'patients' => $patients,
        ]);
    }
}
