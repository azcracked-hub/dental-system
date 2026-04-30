@extends('layouts.admin')

@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Clinical Notes</h1>
    <p class="text-sm text-gray-400">{{ $notes->count() }} total notes</p>
</div>

<div class="space-y-4">
    @forelse($notes as $note)
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">{{ $note->service ?? 'Clinical Note' }}</h3>
                    <p class="text-sm text-gray-400 mt-0.5">Patient: {{ $note->patient->name ?? 'N/A' }}</p>
                </div>
                <span class="text-xs text-gray-400 shrink-0">
                    {{ \Carbon\Carbon::parse($note->updated_at)->format('M d, Y') }}
                </span>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-sm text-gray-600 leading-relaxed">{{ $note->notes }}</p>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-sm">No clinical notes yet</p>
            <p class="text-xs text-gray-300 mt-1">Notes appear here after completing an appointment</p>
        </div>
    @endforelse
</div>

@endsection
