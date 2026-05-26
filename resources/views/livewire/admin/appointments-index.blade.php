<div>
    <x-ui.flash />

    @if($errors->any() && ! $showCreateModal && ! $showCompleteModal && ! $showBillingModal)
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
            <p class="font-semibold mb-1">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">All Appointments</h1>
            <p class="text-sm text-gray-400">{{ $appointments->total() }} total appointments</p>
        </div>
        <button
            wire:click="openCreateModal"
            class="bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold px-4 py-2 rounded-xl transition"
        >
            + New Appointment
        </button>
    </div>

    <div class="space-y-4">
        @forelse($appointments as $appt)
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">{{ $appt->serviceNames() }}</h3>
                        <p class="text-xs text-gray-400">
                            {{ $appt->totalServiceDuration() }} mins • ₱{{ number_format($appt->totalServicePrice(), 2) }}
                        </p>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium
                        @if($appt->status === 'confirmed') bg-green-100 text-green-700
                        @elseif($appt->status === 'pending') bg-yellow-100 text-yellow-700
                        @elseif($appt->status === 'completed') bg-blue-100 text-blue-700
                        @else bg-red-100 text-red-700 @endif">
                        {{ ucfirst($appt->status) }}
                    </span>
                </div>

                <div class="flex items-center gap-6 text-sm text-gray-500 mb-4">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/>
                            <line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/>
                        </svg>
                        {{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke-width="2"/>
                            <polyline points="12 6 12 12 16 14" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        {{ \Carbon\Carbon::parse($appt->time)->format('H:i') }}
                    </span>
                </div>

                @if($appt->status !== 'completed' && $appt->status !== 'canceled')
                    <div class="flex gap-3">
                        <button
                            wire:click="openCompleteModal({{ $appt->id }})"
                            class="flex-1 flex items-center justify-center gap-2 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold py-2.5 rounded-xl transition"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Complete & Add Notes
                        </button>

                        <button
                            wire:click="openBillingModal({{ $appt->id }})"
                            class="flex-1 flex items-center justify-center gap-2 border border-yellow-400 text-yellow-500 hover:bg-yellow-50 text-sm font-semibold py-2.5 rounded-xl transition"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="1" y="4" width="22" height="16" rx="2" stroke-width="2"/>
                                <line x1="1" y1="10" x2="23" y2="10" stroke-width="2"/>
                            </svg>
                            Create Billing
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.5"/>
                    <line x1="3" y1="10" x2="21" y2="10" stroke-width="1.5"/>
                </svg>
                <p class="text-sm">No appointments found</p>
            </div>
        @endforelse
    </div>

    <x-list-pagination :paginator="$appointments" />

    <x-ui.modal :show="$showCreateModal" maxWidth="lg" close-action="closeCreateModal">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-bold text-gray-900">New Appointment</h2>
                <p class="text-sm text-gray-400">Select 1 to 3 services for this appointment</p>
            </div>
            <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>

        @if($showCreateModal && $errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                <p class="font-semibold mb-1">Could not create appointment:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form wire:submit="createAppointment" class="space-y-4">
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Patient</label>
                <select wire:model="patients_id" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
                    <option value="">Select patient</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
                @error('patients_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Doctor</label>
                <select wire:model="doctor_id" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
                    <option value="">Select doctor</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->name }} (Doctor)</option>
                    @endforeach
                </select>
                @error('doctor_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-medium text-gray-700">Services</label>
                    <span class="text-xs font-semibold text-yellow-600">{{ count($service_ids) }} selected (max 3)</span>
                </div>
                <p class="text-xs text-gray-400 mb-3">Pick at least 1 service. You may choose 1, 2, or 3.</p>
                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-100 rounded-xl p-2">
            @foreach($services as $service)
                        @php
                            $selected = in_array($service->id, $service_ids, true);
                            $isDisabled = ! $selected && count($service_ids) >= 3;
                        @endphp
                        <button
                            type="button"
                            wire:click="toggleService({{ $service->id }})"
                            @disabled($isDisabled)
                            class="w-full text-left flex items-start gap-3 p-3 border rounded-xl transition {{ $selected ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200 hover:border-yellow-400 hover:bg-yellow-50' }}"
                        >
                            <span class="mt-1 w-4 h-4 rounded border {{ $selected ? 'bg-yellow-400 border-yellow-500' : 'border-gray-300' }}"></span>
                            <span class="flex-1 min-w-0">
                                <span class="block text-sm font-semibold text-gray-900">{{ $service->name }}</span>
                                <span class="block text-xs text-gray-400">{{ $service->duration_minutes }} mins • ₱{{ number_format($service->price, 2) }}</span>
                            </span>
                        </button>
                    @endforeach
                </div>
                @error('service_ids') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                @error('service_ids.*') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">Date</label>
                    <input type="date" wire:model="date" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
                    @error('date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">Time</label>
                    <input type="time" wire:model="time" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
                    @error('time') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Notes (optional)</label>
                <textarea wire:model="notes" rows="2" placeholder="Additional notes..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300 resize-none"></textarea>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button" wire:click="closeCreateModal"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold py-2.5 rounded-xl transition">
                    Create Appointment
                </button>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.modal :show="$showCompleteModal" maxWidth="md" close-action="closeCompleteModal">
        <div class="flex items-center justify-between mb-1">
            <h2 class="text-base font-bold text-gray-900">Add Clinical Notes</h2>
            <button wire:click="closeCompleteModal" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <p class="text-sm text-gray-400 mb-4">Add notes and complete this appointment.</p>

        <form wire:submit="completeAppointment" class="space-y-4">
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Clinical Notes</label>
                <textarea wire:model="completeNotes" rows="4" required placeholder="Enter your clinical notes and recommendations..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300 resize-none"></textarea>
                @error('completeNotes') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" wire:click="closeCompleteModal"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold py-2.5 rounded-xl transition">
                    Save & Complete Appointment
                </button>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.modal :show="$showBillingModal" maxWidth="md" close-action="closeBillingModal">
        <div class="flex items-center justify-between mb-1">
            <h2 class="text-base font-bold text-gray-900">Create Billing Record</h2>
            <button wire:click="closeBillingModal" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <p class="text-sm text-gray-400 mb-4">Create billing for the selected appointment.</p>

        <form wire:submit="createBilling" class="space-y-4">
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Billing Amount (₱)</label>
                <input type="number" wire:model="billingAmount" required min="0" step="0.01" placeholder="2000"
                    class="w-full border border-yellow-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
                <p class="text-xs text-gray-400 mt-1">Enter the exact amount to be billed to the patient</p>
                @error('billingAmount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" wire:click="closeBillingModal"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold py-2.5 rounded-xl transition">
                    Create Billing
                </button>
            </div>
        </form>
    </x-ui.modal>
</div>
