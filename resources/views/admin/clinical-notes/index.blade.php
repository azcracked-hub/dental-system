@extends('layouts.admin')

@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Clinical Notes</h1>
    <p class="text-sm text-gray-400">
        {{ $clinicalNotes->count() }} total notes recorded
    </p>
</div>

<div class="space-y-4">

@forelse($clinicalNotes as $note)

    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 hover:shadow-md transition">

        <div class="flex items-start justify-between mb-4">

            <div>

                <h3 class="text-base font-semibold text-gray-900">
                    {{ $note->serviceNames() }}
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Patient:
                    <span class="font-medium text-gray-700">
                        {{ $note->patient->name ?? 'N/A' }}
                    </span>
                </p>

                <p class="text-xs text-gray-400 mt-1">
                    {{ $note->totalServiceDuration() }} mins • ₱{{ number_format($note->totalServicePrice(), 2) }}
                </p>

            </div>

            <span class="text-xs text-gray-400 whitespace-nowrap">
                {{ \Carbon\Carbon::parse($note->updated_at)->format('M d, Y') }}
            </span>

        </div>

        <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                {{ $note->notes }}
            </p>
        </div>

    </div>

@empty

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">

        <p class="text-sm font-medium text-gray-600">No clinical notes found</p>
        <p class="text-xs text-gray-400 mt-1">
            Clinical notes will appear here after completing appointments
        </p>

    </div>

@endforelse

</div>

@endsection
