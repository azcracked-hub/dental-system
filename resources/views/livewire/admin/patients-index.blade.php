<div>
    <x-ui.flash />

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Patients</h1>
            <p class="text-sm text-gray-400">
                {{ $patients->total() }} result(s)
                @if($search !== '')
                    for "{{ $search }}"
                @endif
            </p>
        </div>
        <button
            wire:click="openCreateModal"
            class="bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold px-4 py-2 rounded-xl transition"
        >
            + Add Patient
        </button>
    </div>

    <div class="mb-4">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search name, email, or phone..."
            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300"
        >
    </div>

    <div class="space-y-3">
        @forelse($patients as $patient)
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-yellow-50 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $patient->name }}</p>
                        <p class="text-xs text-gray-400">{{ $patient->email }}</p>
                        @if($patient->phone)
                            <p class="text-xs text-gray-400">{{ $patient->phone }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-400">{{ $patient->appointments_count }} appointment(s)</span>
                    <a
                        href="{{ route('admin.patients.show', $patient->id) }}"
                        wire:navigate
                        class="text-xs border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition"
                    >
                        View
                    </a>
                    <x-ui.confirm-button
                        wireMethod="deletePatient"
                        :param="$patient->id"
                        title="Delete patient?"
                        message="This will permanently remove the patient and their login account."
                        confirmLabel="Delete"
                    >
                        <x-slot name="trigger">
                            <button type="button" class="text-xs border border-red-200 text-red-400 hover:bg-red-50 px-3 py-1.5 rounded-lg transition">
                                Delete
                            </button>
                        </x-slot>
                    </x-ui.confirm-button>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-sm">No patients found</p>
            </div>
        @endforelse
    </div>

    <x-list-pagination :paginator="$patients" />

    <x-ui.modal :show="$showCreateModal" maxWidth="md" close-action="closeCreateModal">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-gray-900">Add New Patient</h2>
            <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>

        @if($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                <p class="font-semibold mb-1">Could not add patient. Please fix the following:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form wire:submit="store" class="space-y-4">
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Full Name</label>
                <input type="text" wire:model="name" required placeholder="Juan Dela Cruz"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300 @error('name') border-red-400 @enderror">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Email</label>
                <input type="email" wire:model="email" required placeholder="juan@email.com"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300 @error('email') border-red-400 @enderror">
                @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Phone</label>
                <input type="text" wire:model="phone" placeholder="+63 9XX XXX XXXX"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Password</label>
                <input type="password" wire:model="password" required placeholder="e.g. Password1"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300 @error('password') border-red-400 @enderror">
                <p class="text-xs text-gray-400 mt-1">At least 8 characters with uppercase, lowercase, and a number (example: <strong>Password1</strong>).</p>
                @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Address</label>
                <input type="text" wire:model="address" placeholder="Street, City"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" wire:click="closeCreateModal"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold py-2.5 rounded-xl">
                    Add Patient
                </button>
            </div>
        </form>
    </x-ui.modal>
</div>
