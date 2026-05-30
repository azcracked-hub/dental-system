<div>
    <x-ui.flash />

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">System Admin</h1>
            <p class="text-sm text-gray-400">User management and system overview</p>
        </div>
        <button wire:click="openCreateUser" class="bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold px-4 py-2 rounded-xl">+ Add User</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 border-l-4 border-l-yellow-400">
            <p class="text-sm text-gray-500 mb-1">Total Patients</p>
            <p class="text-3xl font-bold text-gray-900">{{ $totalPatients }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 border-l-4 border-l-blue-400">
            <p class="text-sm text-gray-500 mb-1">Total Appointments</p>
            <p class="text-3xl font-bold text-gray-900">{{ $totalAppointments }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-base font-bold text-gray-900 mb-4">System Users</h2>
        <div class="space-y-3">
            @forelse($users as $user)
                <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $user->email }} • <span class="capitalize">{{ $user->role }}</span></p>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="openEditUser({{ $user->id }})" class="text-xs border border-gray-200 px-2 py-1 rounded-lg hover:bg-gray-50">Edit</button>
                        @if($user->id !== auth()->id())
                            <x-ui.confirm-button wireMethod="deleteUser" :param="$user->id" title="Delete user?" message="This will permanently remove this account." confirmLabel="Delete">
                                <x-slot name="trigger">
                                    <button type="button" class="text-xs border border-red-200 text-red-500 px-2 py-1 rounded-lg hover:bg-red-50">Delete</button>
                                </x-slot>
                            </x-ui.confirm-button>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400">No users found.</p>
            @endforelse
        </div>
    </div>

    <x-ui.modal :show="$showUserModal" maxWidth="md" close-action="closeUserModal">
        <h2 class="text-base font-bold text-gray-900 mb-4">{{ $editingUserId ? 'Edit User' : 'Add User' }}</h2>
        <form wire:submit="saveUser" class="space-y-4">
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Name</label>
                <input type="text" wire:model="name" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Email</label>
                <input type="email" wire:model="email" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Phone</label>
                <input type="text" wire:model="phone" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Role</label>
                <select wire:model="role" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                    <option value="admin">Admin / Doctor</option>
                    <option value="staff">Staff</option>
                    <option value="patient">Patient</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Password {{ $editingUserId ? '(leave blank to keep)' : '' }}</label>
                <input type="password" wire:model="password" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-3">
                <button type="button" wire:click="closeUserModal" class="flex-1 border border-gray-200 py-2 rounded-xl text-sm">Cancel</button>
                <button type="submit" class="flex-1 bg-yellow-400 text-white py-2 rounded-xl text-sm font-semibold">Save</button>
            </div>
        </form>
    </x-ui.modal>
</div>
