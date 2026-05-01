@extends('layouts.staff')

@section('content')

<!-- Root component for Alpine.js Tab State -->
<div x-data="{ activeTab: 'today', searchParams: '' }">

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- 1. Stats Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

        <!-- Today's Schedule -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-400 flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <h3 class="text-gray-500 text-sm font-medium">Today's Schedule</h3>
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="text-3xl font-semibold text-gray-800">{{ $stats['today_count'] }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stats['today_count'] }} confirmed</p>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-400 flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <h3 class="text-gray-500 text-sm font-medium">Pending Approvals</h3>
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <p class="text-3xl font-semibold text-gray-800">{{ $stats['pending_count'] }}</p>
                <p class="text-xs text-gray-400 mt-1">Awaiting confirmation</p>
            </div>
        </div>

        <!-- Total Appointments -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <h3 class="text-gray-500 text-sm font-medium">Total Appointments</h3>
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
            <div>
                <p class="text-3xl font-semibold text-gray-800">{{ $stats['total_count'] }}</p>
                <p class="text-xs text-gray-400 mt-1">All records</p>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500 flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <h3 class="text-gray-500 text-sm font-medium">Pending Payments</h3>
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            <div>
                <p class="text-3xl font-semibold text-gray-800">₱{{ number_format($stats['unpaid_sum'], 0) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stats['unpaid_count'] }} bill(s)</p>
            </div>
        </div>
    </div>

    <!-- 2. Tabs Navigation -->
    <div class="inline-flex bg-white rounded-full p-1 shadow-sm mb-8 border border-gray-100">
        <button @click="activeTab = 'today'" :class="{ 'bg-yellow-500 text-white': activeTab === 'today', 'text-gray-600 hover:text-gray-800': activeTab !== 'today' }" class="px-5 py-2 text-sm font-medium rounded-full transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Today's Schedule
        </button>
        <button @click="activeTab = 'all'" :class="{ 'bg-yellow-500 text-white': activeTab === 'all', 'text-gray-600 hover:text-gray-800': activeTab !== 'all' }" class="px-5 py-2 text-sm font-medium rounded-full transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            All Appointments
        </button>
        <button @click="activeTab = 'patients'" :class="{ 'bg-yellow-500 text-white': activeTab === 'patients', 'text-gray-600 hover:text-gray-800': activeTab !== 'patients' }" class="px-5 py-2 text-sm font-medium rounded-full transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            Patient Records
        </button>
        <button @click="activeTab = 'billing'" :class="{ 'bg-yellow-500 text-white': activeTab === 'billing', 'text-gray-600 hover:text-gray-800': activeTab !== 'billing' }" class="px-5 py-2 text-sm font-medium rounded-full transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            Billing Records
        </button>
    </div>

    <!-- 3. Tab Contents -->

    <!-- TAB: Today's Schedule -->
    <div x-show="activeTab === 'today'" style="display: none;" x-transition>
        <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-800">Today's Appointments</h2>
            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->format('l, F d, Y') }}</p>
        </div>

        @if($todayAppointments->isEmpty())
            <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100 flex flex-col items-center justify-center">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p class="text-gray-500 font-medium">No appointments scheduled for today</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($todayAppointments as $apt)
                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 flex justify-between items-center">
                        <div>
                            <h4 class="font-bold text-gray-800">{{ $apt->service->name ?? 'General Service' }}</h4>
                            <p class="text-sm text-gray-500 flex items-center gap-2 mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                {{ $apt->patient->name }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-800 font-semibold">{{ \Carbon\Carbon::parse($apt->time)->format('H:i') }}</p>
                            <p class="text-xs text-gray-400 mt-1">Dr. {{ $apt->doctor->name }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- TAB: All Appointments -->
    <div x-show="activeTab === 'all'" style="display: none;" x-transition>
        <!-- Alpine-powered client-side search -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-2 mb-6 flex items-center">
            <svg class="w-5 h-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input x-model="searchParams" type="text" placeholder="Search by patient name, doctor, or service..." class="w-full border-none focus:ring-0 text-sm ml-2 text-gray-600 placeholder-gray-400">
        </div>

        <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-800">All Appointments</h2>
            <p class="text-sm text-gray-500">{{ $allAppointments->count() }} total records</p>
        </div>

        <div class="space-y-4">
            @foreach($allAppointments as $apt)
                <!-- The x-show logic applies the search filter to patient, doctor, and service names -->
                <div x-show="searchParams === '' || '{{ strtolower($apt->patient->name ?? '') }}'.includes(searchParams.toLowerCase()) || '{{ strtolower($apt->doctor->name ?? '') }}'.includes(searchParams.toLowerCase()) || '{{ strtolower($apt->service->name ?? '') }}'.includes(searchParams.toLowerCase())"
                     class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 flex justify-between items-start">
                    <div class="space-y-3 w-full">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <h4 class="font-bold text-gray-800">{{ $apt->service->name ?? 'General Service' }}</h4>

                                <!-- Status Badges -->
                                @if($apt->status == 'confirmed')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-green-200">confirmed</span>
                                @elseif($apt->status == 'completed')
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-blue-200">completed</span>
                                @elseif($apt->status == 'pending')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-yellow-200">pending</span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-red-200">cancelled</span>
                                @endif
                            </div>

                            <!-- Cancel Button (Only if not completed or cancelled) -->
                            @if(!in_array($apt->status, ['completed', 'canceled']))
                                <form action="{{ route('staff.appointments.cancel', $apt->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-red-500 hover:bg-red-50 border border-red-500 font-medium rounded-lg text-xs px-4 py-1.5 transition">
                                        Cancel
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm text-gray-500">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                {{ $apt->patient->name ?? 'N/A' }}
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Dr. {{ $apt->doctor->name ?? 'N/A' }}
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ \Carbon\Carbon::parse($apt->date)->format('M d, Y') }}
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ \Carbon\Carbon::parse($apt->time)->format('H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB: Patient Records -->
    <div x-show="activeTab === 'patients'" style="display: none;" x-transition>
        <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-800">Patient Records</h2>
            <p class="text-sm text-gray-500">View patient appointment history</p>
        </div>

        <div class="space-y-4">
            @foreach($patients as $patient)
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                    <h4 class="font-bold text-gray-800">{{ $patient->name }}</h4>
                    <p class="text-sm text-gray-500 mt-1">Total Appointments: {{ $patient->total_appointments }}</p>

                    <div class="flex gap-3 mt-3">
                        <span class="bg-gray-100 text-gray-700 text-xs font-medium px-2.5 py-0.5 rounded-full border border-gray-200">
                            Confirmed: {{ $patient->confirmed_count }}
                        </span>
                        <span class="bg-gray-100 text-gray-700 text-xs font-medium px-2.5 py-0.5 rounded-full border border-gray-200">
                            Completed: {{ $patient->completed_count }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB: Billing Records -->
    <div x-show="activeTab === 'billing'" style="display: none;" x-transition>

        <!-- Info Alert -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 flex items-start gap-3 text-sm text-yellow-800">
            <span class="font-bold">Note:</span> Staff members cannot modify billing amounts. Only doctors have the authority to create or adjust billing records.
        </div>

        <div class="space-y-4">
            @foreach($billings as $bill)
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="font-bold text-gray-800">{{ $bill->appointment->service->name ?? $bill->description }}</h4>
                            <p class="text-sm text-gray-500 mt-1">{{ $bill->patient->name ?? 'Unknown' }} &bull; {{ \Carbon\Carbon::parse($bill->created_at)->format('F d, Y') }}</p>
                        </div>
                        @if($bill->status == 'paid')
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-green-200">paid</span>
                        @else
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-red-200">unpaid</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 text-sm">
                        <div class="text-gray-500">Amount:</div>
                        <div class="text-right font-medium text-gray-800">₱{{ number_format($bill->amount, 0) }}</div>

                        <div class="text-gray-500 mt-2">Payment Method:</div>
                        <div class="text-right font-medium text-gray-800 mt-2">{{ ucfirst($bill->payment_method ?? 'N/A') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
