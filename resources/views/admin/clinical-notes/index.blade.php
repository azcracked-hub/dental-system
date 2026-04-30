@extends('layouts.admin')

@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Clinical Notes</h1>
    <p class="text-sm text-gray-400">
        {{ $notes->count() }} total notes recorded
    </p>
</div>

<div class="space-y-4">

@forelse($notes as $note)

    @php
        // ✅ FIX: $note IS ALREADY an Appointment
        $service = $note->service;
        $patient = $note->patient;
    @endphp

    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 hover:shadow-md transition">

        {{-- Header --}}
        <div class="flex items-start justify-between mb-4">

            <div>

                {{-- Service Name --}}
                <h3 class="text-base font-semibold text-gray-900">
                    {{ $service->name ?? 'Clinical Note' }}
                </h3>

                {{-- Patient --}}
                <p class="text-sm text-gray-500 mt-1">
                    Patient:
                    <span class="font-medium text-gray-700">
                        {{ $patient->name ?? 'N/A' }}
                    </span>
                </p>

                {{-- Service Details --}}
                @if($service)
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $service->duration_minutes }} mins • ₱{{ number_format($service->price, 2) }}
                    </p>
                @endif

            </div>

            {{-- Date --}}
            <span class="text-xs text-gray-400 whitespace-nowrap">
                {{ \Carbon\Carbon::parse($note->updated_at)->format('M d, Y') }}
            </span>

        </div>

        {{-- Notes --}}
        <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                {{ $note->notes }}
            </p>
        </div>

    </div>

@empty

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">

        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>

        <p class="text-sm font-medium text-gray-600">No clinical notes found</p>
        <p class="text-xs text-gray-400 mt-1">
            Clinical notes will appear here after completing appointments
        </p>

    </div>

@endforelse

</div>

@endsection
