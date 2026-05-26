@extends('layouts.admin')

@section('content')

@if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
        <p class="font-semibold mb-1">Could not create appointment:</p>
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
    <button onclick="document.getElementById('createModal').classList.remove('hidden')"
        class="bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
        + New Appointment
    </button>
</div>

{{-- Appointment Cards --}}
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
                    {{-- Complete & Add Notes --}}
                    <button
                        onclick="openNotesModal({{ $appt->id }}, '{{ addslashes($appt->patient->name ?? '') }}', '{{ addslashes($appt->serviceNames()) }}')"
                        class="flex-1 flex items-center justify-center gap-2 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold py-2.5 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Complete & Add Notes
                    </button>

                    {{-- Create Billing --}}
                    <button
                       onclick="openBillingModal({{ $appt->id }},'{{ addslashes($appt->patient->name ?? '') }}','{{ addslashes($appt->serviceNames()) }}',{{ $appt->totalServicePrice() }})"
                        class="flex-1 flex items-center justify-center gap-2 border border-yellow-400 text-yellow-500 hover:bg-yellow-50 text-sm font-semibold py-2.5 rounded-xl transition">
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

{{-- ===== CREATE APPOINTMENT MODAL ===== --}}
<div id="createModal" class="{{ $errors->has('service_ids') || $errors->has('service_ids.*') ? '' : 'hidden' }} fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-bold text-gray-900">New Appointment</h2>
                <p class="text-sm text-gray-400">Select 1 to 3 services for this appointment</p>
            </div>
            <button onclick="document.getElementById('createModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form action="{{ route('admin.appointments.store') }}" method="POST" class="space-y-4" id="admin-appointment-form">
            @csrf
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Patient</label>
                <select name="patients_id" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
                    <option value="">Select patient</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}" @selected(old('patients_id') == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
                @error('patients_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Doctor</label>
                <select name="doctor_id" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
                    <option value="">Select doctor</option>
                    @foreach(\App\Models\User::doctors()->get() as $admin)
                        <option value="{{ $admin->id }}" @selected(old('doctor_id') == $admin->id)>{{ $admin->name }} (Doctor)</option>
                    @endforeach
                </select>
                @error('doctor_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-medium text-gray-700">Services</label>
                    <span id="admin-service-count" class="text-xs font-semibold text-yellow-600">0 selected (max 3)</span>
                </div>
                <p class="text-xs text-gray-400 mb-3">Pick at least 1 service. You may choose 1, 2, or 3 — not required to select all three.</p>
                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-100 rounded-xl p-2">
                    @foreach($services as $service)
                        <label class="admin-service-option flex items-start gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-yellow-400 hover:bg-yellow-50 transition has-[:checked]:border-yellow-500 has-[:checked]:bg-yellow-50">
                            <input type="checkbox" name="service_ids[]" value="{{ $service->id }}"
                                class="mt-1 w-4 h-4 rounded border-gray-300 text-yellow-500 focus:ring-yellow-400 admin-service-checkbox"
                                @checked(is_array(old('service_ids')) && in_array($service->id, old('service_ids')))>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900">{{ $service->name }}</p>
                                <p class="text-xs text-gray-400">{{ $service->duration_minutes }} mins • ₱{{ number_format($service->price, 2) }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('service_ids') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                @error('service_ids.*') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">Date</label>
                    <input type="date" name="date" required value="{{ old('date') }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
                    @error('date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">Time</label>
                    <input type="time" name="time" required value="{{ old('time') }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
                    @error('time') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Notes (optional)</label>
                <textarea name="notes" rows="2" placeholder="Additional notes..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300 resize-none">{{ old('notes') }}</textarea>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition">Cancel</button>
                <button type="submit" id="admin-create-submit"
                    class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold py-2.5 rounded-xl transition">Create Appointment</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== COMPLETE & ADD NOTES MODAL ===== --}}
<div id="notesModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-1">
            <h2 class="text-base font-bold text-gray-900">Add Clinical Notes</h2>
            <button onclick="document.getElementById('notesModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <p id="notesSubtitle" class="text-sm text-gray-400 mb-4"></p>
        <form id="notesForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Clinical Notes</label>
                <textarea name="notes" rows="4" required placeholder="Enter your clinical notes and recommendations..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300 resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('notesModal').classList.add('hidden')"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition">Cancel</button>
                <button type="submit"
                    class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold py-2.5 rounded-xl transition">Save & Complete Appointment</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== CREATE BILLING MODAL ===== --}}
<div id="billingModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-1">
            <h2 class="text-base font-bold text-gray-900">Create Billing Record</h2>
            <button onclick="document.getElementById('billingModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <p id="billingSubtitle" class="text-sm text-gray-400 mb-4"></p>
        <form action="{{ route('admin.billing.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="appointment_id" id="billingAppointmentId">
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Billing Amount (₱)</label>
                <input type="number" name="amount" required min="0" step="0.01" placeholder="2000"
                    class="w-full border border-yellow-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
                <p class="text-xs text-gray-400 mt-1">Enter the exact amount to be billed to the patient</p>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('billingModal').classList.add('hidden')"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition">Cancel</button>
                <button type="submit"
                    class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold py-2.5 rounded-xl transition">Create Billing</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openNotesModal(id, patient, service) {
    document.getElementById('notesSubtitle').textContent =
        'Add notes for ' + patient + ' - ' + service;

    let url = "{{ route('admin.appointments.complete', ':id') }}";
    url = url.replace(':id', id);
    console.log(url);

    document.getElementById('notesForm').action = url;

    document.getElementById('notesModal').classList.remove('hidden');
}

function openBillingModal(id, patient, service, price) {
    document.getElementById('billingSubtitle').textContent =
        'Create billing for ' + patient + ' - ' + service;
    document.getElementById('billingAppointmentId').value = id;
    document.querySelector('#billingModal input[name="amount"]').value = price;
    document.getElementById('billingModal').classList.remove('hidden');
}

function updateAdminServiceCount() {
    const checked = document.querySelectorAll('.admin-service-checkbox:checked');
    const countEl = document.getElementById('admin-service-count');
    if (countEl) {
        countEl.textContent = checked.length + ' selected (max 3)';
    }
}

document.querySelectorAll('.admin-service-checkbox').forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
        const checked = document.querySelectorAll('.admin-service-checkbox:checked');
        if (checked.length > 3) {
            this.checked = false;
            alert('You can select up to 3 services only.');
        }
        updateAdminServiceCount();
    });
});

updateAdminServiceCount();
</script>
@endpush
