<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithAlerts;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class SystemAdminIndex extends Component
{
    use WithAlerts;

    public bool $showUserModal = false;
    public ?int $editingUserId = null;
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $role = 'staff';
    public string $password = '';

    public function openCreateUser(): void
    {
        $this->resetValidation();
        $this->reset(['editingUserId', 'name', 'email', 'phone', 'password']);
        $this->role = 'staff';
        $this->showUserModal = true;
    }

    public function openEditUser(int $id): void
    {
        $user = User::findOrFail($id);
        $this->resetValidation();
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->role = $user->role;
        $this->password = '';
        $this->showUserModal = true;
    }

    public function closeUserModal(): void
    {
        $this->showUserModal = false;
    }

    public function saveUser(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editingUserId)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,staff,patient',
        ];

        if ($this->editingUserId) {
            if ($this->password !== '') {
                $rules['password'] = ['required', Password::min(8)->mixedCase()->numbers()];
            }
        } else {
            $rules['password'] = ['required', Password::min(8)->mixedCase()->numbers()];
        }

        $validated = $this->validate($rules);

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?: null,
                'role' => $validated['role'],
            ]);
            if ($this->password !== '') {
                $user->update(['password' => Hash::make($this->password)]);
            }
            $this->alertSuccess('User updated.');
        } else {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?: null,
                'role' => $validated['role'],
                'password' => Hash::make($validated['password']),
            ]);
            $this->alertSuccess('User created.');
        }

        $this->closeUserModal();
    }

    public function deleteUser(int $id): void
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            $this->alertError('You cannot delete your own account.');
            return;
        }
        $user->delete();
        $this->alertSuccess('User deleted.');
    }

    public function render()
    {
        return view('livewire.admin.system-admin-index', [
            'users' => User::orderBy('name')->get(),
            'totalPatients' => Patient::count(),
            'totalAppointments' => Appointment::count(),
        ]);
    }
}
