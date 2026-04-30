@extends('layouts.admin')

@section('content')

@if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Patients</h1>
        <p class="text-sm text-gray-400">{{ $patients->count() }} total patients</p>
    </div>
    <button onclick="document.getElementById('createPatientModal').classList.remove('hidden')"
        class="bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
        + Add Patient
    </button>
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
                <a href="{{ route('admin.patients.show', $patient->id) }}"
                    class="text-xs border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition">
                    View
                </a>
                <form method="POST" action="{{ route('admin.patients.destroy', $patient->id) }}"
                    onsubmit="return confirm('Delete this patient?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="text-xs border border-red-200 text-red-400 hover:bg-red-50 px-3 py-1.5 rounded-lg transition">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-sm">No patients yet</p>
        </div>
    @endforelse
</div>

{{-- Add Patient Modal --}}
<div id="createPatientModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-gray-900">Add New Patient</h2>
            <button onclick="document.getElementById('createPatientModal').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form action="{{ route('admin.patients.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Full Name</label>
                <input type="text" name="name" required placeholder="Juan Dela Cruz"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Email</label>
                <input type="email" name="email" required placeholder="juan@email.com"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Phone</label>
                <input type="text" name="phone" placeholder="+63 9XX XXX XXXX"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Address</label>
                <input type="text" name="address" placeholder="Street, City"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button"
                    onclick="document.getElementById('createPatientModal').classList.add('hidden')"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold py-2.5 rounded-xl">
                    Add Patient
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
