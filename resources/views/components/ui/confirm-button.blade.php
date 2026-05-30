@props([
    'wireMethod',
    'param' => null,
    'title' => 'Confirm action',
    'message' => 'Are you sure you want to continue?',
    'confirmLabel' => 'Confirm',
    'cancelLabel' => 'Cancel',
    'variant' => 'danger',
])

@php
    $confirmClass = $variant === 'danger'
        ? 'bg-red-500 hover:bg-red-600 text-white'
        : 'bg-yellow-400 hover:bg-yellow-500 text-white';

    $wireCall = $param !== null
        ? "\$wire.{$wireMethod}({$param})"
        : "\$wire.{$wireMethod}()";
@endphp

<div x-data="{ open: false }" class="inline">
    <span @click.stop="open = true">
        {{ $trigger }}
    </span>

    @teleport('body')
        <div
            x-show="open"
            x-cloak
            class="fixed inset-0 z-[250] flex items-center justify-center p-4"
            @keydown.escape.window="open = false"
        >
            <div class="absolute inset-0 bg-black/40" @click="open = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6" @click.stop>
                <h3 class="text-base font-bold text-gray-900 mb-1">{{ $title }}</h3>
                <p class="text-sm text-gray-500 mb-5">{{ $message }}</p>
                <div class="flex gap-3">
                    <button
                        type="button"
                        @click="open = false"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50"
                    >
                        {{ $cancelLabel }}
                    </button>
                    <button
                        type="button"
                        @click="open = false; {{ $wireCall }}"
                        class="flex-1 text-sm font-semibold py-2.5 rounded-xl {{ $confirmClass }}"
                    >
                        {{ $confirmLabel }}
                    </button>
                </div>
            </div>
        </div>
    @endteleport
</div>
