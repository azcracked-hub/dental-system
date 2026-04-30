@extends('layouts.admin')

@section('content')

{{-- Back button --}}
<div class="mb-5">
    <a href="{{ route('admin.patients.index') }}"
        class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Patients
    </a>
</div>

{{-- Patient Profile Card --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-yellow-50 border border-yellow-100 flex items-center justify-center shrink-0">
                <svg class="w-7 h-7 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $patient->name }}</h1>
                <p class="text-sm text-gray-400">{{ $patient->email }}</p>
                @if($patient->phone)
                    <p class="text-sm text-gray-400">{{ $patient->phone }}</p>
                @endif
                @if($patient->address)
                    <p class="text-sm text-gray-400">{{ $patient->address }}</p>
                @endif
            </div>
        </div>
        <button onclick="document.getElementById('editPatientModal').classList.remove('hidden')"
            class="border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded-xl transition">
            Edit Info
        </button>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-50">
        <div class="text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $patient->appointments->count() }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Total Appointments</p>
        </div>
        <div class="text-center">
            <p class="text-2xl font-bold text-gray-900">
                {{ $patient->appointments->where('status', 'completed')->count() }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">Completed</p>
        </div>
        <div class="text-center">
            <p class="text-2xl font-bold text-yellow-500">
                ₱{{ number_format($patient->billings->sum('amount'), 0) }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">Total Billed</p>
        </div>
    </div>
</div>

{{-- Appointments History --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 mb-6">
    <h2 class="text-base font-bold text-gray-900 mb-1">Appointment History</h2>
    <p class="text-sm text-gray-400 mb-5">{{ $patient->appointments->count() }} total appointments</p>

    <div class="space-y-3">
        @forelse($patient->appointments->sortByDesc('date') as $appt)
            <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $appt->service ?? 'Appointment' }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }}
                        at {{ \Carbon\Carbon::parse($appt->time)->format('H:i') }}
                    </p>
                    @if($appt->notes)
                        <p class="text-xs text-gray-500 mt-1 italic">{{ Str::limit($appt->notes, 80) }}</p>
                    @endif
                </div>
                <span class="text-xs px-2.5 py-1 rounded-full font-medium shrink-0
                    @if($appt->status === 'confirmed') bg-green-100 text-green-700
                    @elseif($appt->status === 'pending') bg-yellow-100 text-yellow-700
                    @elseif($appt->status === 'completed') bg-blue-100 text-blue-700
                    @else bg-red-100 text-red-700 @endif">
                    {{ ucfirst($appt->status) }}
                </span>
            </div>
        @empty
            <p class="text-sm text-gray-400 text-center py-6">No appointments yet</p>
        @endforelse
    </div>
</div>

{{-- Billing Records --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
    <h2 class="text-base font-bold text-gray-900 mb-1">Billing Records</h2>
    <p class="text-sm text-gray-400 mb-5">{{ $patient->billings->count() }} total records</p>

    <div class="space-y-3">
        @forelse($patient->billings->sortByDesc('created_at') as $bill)
            <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-sm font-semibold text-gray-800">
                        {{ $bill->description ?? $bill->appointment->service ?? 'Billing' }}
                    </p>
                    <div class="text-xs text-gray-400 mt-0.5 space-y-0.5">
                        <p>Date: {{ \Carbon\Carbon::parse($bill->created_at)->format('M d, Y') }}</p>
                        @if($bill->payment_method)
                            <p>Payment Method: {{ $bill->payment_method }}</p>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-base font-bold text-yellow-500">₱{{ number_format($bill->amount, 0) }}</p>
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium inline-block mt-1
                        {{ $bill->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $bill->status }}
                    </span>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400 text-center py-6">No billing records yet</p>
        @endforelse
    </div>
</div>

{{-- Edit Patient Modal --}}
<div id="editPatientModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-gray-900">Edit Patient</h2>
            <button onclick="document.getElementById('editPatientModal').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form action="{{ route('admin.patients.update', $patient) }}" method="POST" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Full Name</label>
                <input type="text" name="name" value="{{ $patient->name }}" required
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Email</label>
                <input type="email" name="email" value="{{ $patient->email }}" required
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Phone</label>
                <input type="text" name="phone" value="{{ $patient->phone }}"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Address</label>
                <input type="text" name="address" value="{{ $patient->address }}"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button"
                    onclick="document.getElementById('editPatientModal').classList.add('hidden')"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold py-2.5 rounded-xl">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
