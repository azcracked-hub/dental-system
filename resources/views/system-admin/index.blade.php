@extends('layouts.admin')

@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">System Admin</h1>
    <p class="text-sm text-gray-400">Manage users and system overview</p>
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

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-3">
        @foreach($users as $user)
            <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $user->name }}</p>
                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium
                        {{ $user->role === 'admin' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                    {{-- Change role button --}}
                    <form method="POST" action="{{ route('system-admin.toggle-role', $user->id) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                            class="text-xs text-gray-400 hover:text-yellow-600 border border-gray-200 hover:border-yellow-300 px-2.5 py-1 rounded-lg transition">
                            {{ $user->role === 'admin' ? 'Set Patient' : 'Set Admin' }}
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>

@endsection
