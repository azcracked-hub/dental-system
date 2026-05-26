@props(['show' => false, 'maxWidth' => 'md'])

@php
    $maxWidthClass = match ($maxWidth) {
        'sm' => 'max-w-sm',
        'lg' => 'max-w-lg',
        default => 'max-w-md',
    };
@endphp

@if ($show)
    @teleport('body')
        <div
            {{ $attributes->merge(['class' => 'fixed inset-0 z-[200] flex items-center justify-center p-4']) }}
            x-data
            x-on:keydown.escape.window="$wire.{{ $attributes->get('close-action', 'closeModal') }}()"
        >
            <div class="absolute inset-0 bg-black/40" wire:click="{{ $attributes->get('close-action', 'closeModal') }}"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full {{ $maxWidthClass }} max-h-[90vh] overflow-y-auto p-6">
                {{ $slot }}
            </div>
        </div>
    @endteleport
@endif
