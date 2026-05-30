<div>
    <x-ui.flash />

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">
        <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 border-l-4 border-l-yellow-400">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/>
                    <line x1="16" y1="2" x2="16" y2="6" stroke-width="2"/>
                    <line x1="8" y1="2" x2="8" y2="6" stroke-width="2"/>
                    <line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/>
                </svg>
                <span class="text-sm font-semibold text-gray-700">Your Next Appointment</span>
            </div>

            @if($nextAppointment)
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-1">
                            {{ $nextAppointment->serviceNames() }}
                        </h2>
                        <p class="text-sm text-gray-500 mb-3">
                            with {{ $nextAppointment->doctor->name ?? 'Estandarte' }}
                        </p>
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6" stroke-width="2"/>
                                    <line x1="8" y1="2" x2="8" y2="6" stroke-width="2"/>
                                    <line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/>
                                </svg>
                                {{ \Carbon\Carbon::parse($nextAppointment->date)->format('l, M d, Y') }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                    <polyline points="12 6 12 12 16 14" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                {{ $nextAppointment->time }}
                            </span>
                        </div>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium
                        @if($nextAppointment->status === 'confirmed') bg-green-100 text-green-700
                        @elseif($nextAppointment->status === 'pending') bg-yellow-100 text-yellow-700
                        @else bg-blue-100 text-blue-700 @endif">
                        {{ $nextAppointment->status }}
                    </span>
                </div>
                @if($nextAppointment && ! in_array($nextAppointment->status, ['completed', 'canceled']))
                    <x-ui.confirm-button
                        wireMethod="cancelAppointment"
                        :param="$nextAppointment->id"
                        title="Cancel appointment?"
                        message="Your upcoming appointment will be marked as canceled."
                        confirmLabel="Yes, cancel"
                        variant="primary"
                    >
                        <x-slot name="trigger">
                            <button type="button" class="text-sm px-4 py-1.5 rounded-lg transition-all text-red-500 border border-red-300 hover:bg-red-50">
                                Cancel Appointment
                            </button>
                        </x-slot>
                    </x-ui.confirm-button>
                @elseif($nextAppointment)
                    <button type="button" disabled class="text-sm px-4 py-1.5 rounded-lg text-gray-400 border border-gray-200 bg-gray-50 cursor-not-allowed">
                        Cannot Cancel
                    </button>
                @endif
            @else
                <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                    <svg class="w-14 h-14 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.5"/>
                        <line x1="16" y1="2" x2="16" y2="6" stroke-width="1.5"/>
                        <line x1="8" y1="2" x2="8" y2="6" stroke-width="1.5"/>
                        <line x1="3" y1="10" x2="21" y2="10" stroke-width="1.5"/>
                    </svg>
                    <p class="text-sm text-gray-500 mb-4">No upcoming appointments</p>
                    <a
                        href="{{ route('patient.appointments.book') }}"
                        wire:navigate
                        class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <line x1="12" y1="5" x2="12" y2="19" stroke-width="2" stroke-linecap="round"/>
                            <line x1="5" y1="12" x2="19" y2="12" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Book Your First Appointment
                    </a>
                </div>
            @endif
        </div>

        <div class="flex flex-col gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 border-l-4 border-l-blue-400 flex-1">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-medium text-gray-600">Total Visits</p>
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-1">{{ $totalVisits }}</h2>
                <p class="text-xs text-gray-400">Completed appointments</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 border-l-4 border-l-yellow-400 flex-1">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-medium text-gray-600">Pending Balance</p>
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="2"/>
                        <line x1="2" y1="10" x2="22" y2="10" stroke-width="2"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-1">₱{{ number_format($pendingBalance, 0) }}</h2>
                <p class="text-xs text-gray-400">{{ $unpaidBills }} unpaid bill(s)</p>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <a
            href="{{ route('patient.appointments.book') }}"
            wire:navigate
            class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all shadow-sm"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19" stroke-width="2" stroke-linecap="round"/>
                <line x1="5" y1="12" x2="19" y2="12" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Book New Appointment
        </a>
    </div>

    <div class="inline-flex items-center gap-1 bg-white border border-gray-200 rounded-full px-1.5 py-1.5 mb-6 shadow-sm">
        <button
            wire:click="setActiveTab('appointments')"
            @class([
                'flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-semibold transition-all',
                'bg-yellow-500 text-white' => $activeTab === 'appointments',
                'text-gray-500 hover:bg-gray-100 hover:text-gray-900' => $activeTab !== 'appointments',
            ])
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/>
                <line x1="16" y1="2" x2="16" y2="6" stroke-width="2"/>
                <line x1="8" y1="2" x2="8" y2="6" stroke-width="2"/>
                <line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/>
            </svg>
            My Appointments
        </button>
        <button
            wire:click="setActiveTab('notes')"
            @class([
                'flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-semibold transition-all',
                'bg-yellow-500 text-white' => $activeTab === 'notes',
                'text-gray-500 hover:bg-gray-100 hover:text-gray-900' => $activeTab !== 'notes',
            ])
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Doctor Notes
        </button>
        <button
            wire:click="setActiveTab('billing')"
            @class([
                'flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-semibold transition-all',
                'bg-yellow-500 text-white' => $activeTab === 'billing',
                'text-gray-500 hover:bg-gray-100 hover:text-gray-900' => $activeTab !== 'billing',
            ])
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="2"/>
                <line x1="2" y1="10" x2="22" y2="10" stroke-width="2"/>
            </svg>
            Billing & Payments
        </button>
    </div>

    @if($activeTab === 'appointments')
        <div>
            <h2 class="text-base font-bold text-gray-900 mb-1">All Appointments</h2>
            <p class="text-sm text-gray-400 mb-4">{{ $appointments->total() }} total appointments</p>

            @forelse($appointments as $appt)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-3 border-l-4
                    @if($appt->status === 'confirmed') border-l-green-400
                    @elseif($appt->status === 'pending') border-l-yellow-400
                    @elseif($appt->status === 'completed') border-l-blue-400
                    @else border-l-red-400 @endif">
                    <div class="flex items-center gap-3 mb-2">
                        <h3 class="text-sm font-bold text-gray-900">
                            {{ $appt->serviceNames() }}
                        </h3>
                        <span class="text-xs px-2.5 py-0.5 rounded-full font-medium
                            @if($appt->status === 'confirmed') bg-green-100 text-green-700
                            @elseif($appt->status === 'pending') bg-yellow-100 text-yellow-700
                            @elseif($appt->status === 'completed') bg-blue-100 text-blue-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ $appt->status }}
                        </span>
                    </div>

                    <p class="text-xs text-gray-500 flex items-center gap-1.5 mb-3">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        {{ $appt->doctor->name ?? 'Estandarte' }}
                    </p>

                    <div class="flex items-center justify-between text-xs text-gray-500 pb-3 border-b border-gray-100">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/>
                                <line x1="16" y1="2" x2="16" y2="6" stroke-width="2"/>
                                <line x1="8" y1="2" x2="8" y2="6" stroke-width="2"/>
                                <line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/>
                            </svg>
                            {{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                <polyline points="12 6 12 12 16 14" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            {{ $appt->time }}
                        </span>
                    </div>

                    @if(in_array($appt->status, ['confirmed', 'pending'], true))
                        <x-ui.confirm-button
                            wireMethod="cancelAppointment"
                            :param="$appt->id"
                            title="Cancel appointment?"
                            message="This appointment will be marked as canceled."
                            confirmLabel="Cancel"
                            variant="primary"
                        >
                            <x-slot name="trigger">
                                <button type="button" class="mt-3 text-xs px-3 py-1.5 rounded-lg transition-all text-red-500 border border-red-300 hover:bg-red-50">
                                    Cancel Appointment
                                </button>
                            </x-slot>
                        </x-ui.confirm-button>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.5"/>
                        <line x1="16" y1="2" x2="16" y2="6" stroke-width="1.5"/>
                        <line x1="8" y1="2" x2="8" y2="6" stroke-width="1.5"/>
                        <line x1="3" y1="10" x2="21" y2="10" stroke-width="1.5"/>
                    </svg>
                    <p class="text-sm">No appointments yet</p>
                </div>
            @endforelse

            <x-list-pagination :paginator="$appointments->appends(['tab' => 'appointments'])" />
        </div>
    @endif

    @if($activeTab === 'notes')
        <div>
            <h2 class="text-base font-bold text-gray-900 mb-1">Treatment History</h2>
            <p class="text-sm text-gray-400 mb-4">Clinical notes from your doctor</p>

            @forelse($clinicalNotes as $note)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-3 border-l-4 border-l-blue-400">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"
                                          stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <h3 class="text-sm font-bold text-gray-900">
                                {{ $note->serviceNames() }}
                            </h3>
                        </div>
                        <span class="text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($note->updated_at)->format('M d, Y') }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mb-3">
                        {{ $note->doctor->name ?? 'N/A' }}
                    </p>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-sm text-gray-700 leading-relaxed">
                            {{ $note->notes }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm">No clinical notes yet</p>
                </div>
            @endforelse
        </div>
    @endif

    @if($activeTab === 'billing')
        <div>
            <h2 class="text-base font-bold text-gray-900 mb-1">Billing & Payments</h2>
            <p class="text-sm text-gray-400 mb-4">Manage your payments and invoices</p>

            @forelse($billings as $bill)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-3">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">
                                {{ $bill->description ?? 'Service' }}
                            </h3>
                            <p class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($bill->created_at)->format('F d, Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-base font-bold text-yellow-600">
                                ₱{{ number_format($bill->amount, 0) }}
                            </p>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                @if($bill->status === 'paid') bg-green-100 text-green-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $bill->status }}
                            </span>
                        </div>
                    </div>
                    @if($bill->payment_method)
                        <p class="text-xs text-gray-500 mt-3 pt-3 border-t border-gray-50">
                            Payment Method: {{ $bill->payment_method }}
                        </p>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="1.5"/>
                        <line x1="2" y1="10" x2="22" y2="10" stroke-width="1.5"/>
                    </svg>
                    <p class="text-sm">No billing records yet</p>
                </div>
            @endforelse
        </div>
    @endif
</div>
