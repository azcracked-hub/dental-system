@props([
    'type' => 'info',
    'dismissible' => true,
])

@php
    $styles = match ($type) {
        'success' => 'bg-green-50 border-green-200 text-green-800',
        'error' => 'bg-red-50 border-red-200 text-red-800',
        'warning' => 'bg-amber-50 border-amber-200 text-amber-900',
        default => 'bg-blue-50 border-blue-200 text-blue-800',
    };

    $icons = match ($type) {
        'success' => 'M5 13l4 4L19 7',
        'error' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'warning' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        default => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    };
@endphp

<div
    {{ $attributes->merge(['class' => "mb-4 px-4 py-3 border rounded-xl text-sm flex items-start gap-3 {$styles}"]) }}
    role="alert"
    @if($dismissible) x-data="{ show: true }" x-show="show" x-transition @endif
>
    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons }}"/>
    </svg>
    <div class="flex-1 min-w-0">{{ $slot }}</div>
    @if($dismissible)
        <button type="button" @click="show = false" class="shrink-0 opacity-60 hover:opacity-100 leading-none" aria-label="Dismiss">&times;</button>
    @endif
</div>
