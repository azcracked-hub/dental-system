@props([
    'size' => 'md',
])

@php
    $wrapperClass = match ($size) {
        'sm' => 'w-8 h-8 rounded-lg',
        'sidebar' => 'w-9 h-9 rounded-lg',
        'md' => 'w-10 h-10 rounded-xl',
        'lg' => 'w-[100px] h-[100px] rounded-[14px]',
        default => 'w-10 h-10 rounded-xl',
    };
@endphp

<div {{ $attributes->merge(['class' => $wrapperClass.' overflow-hidden shrink-0 bg-[#0d0d0d] border border-yellow-500/20 shadow-lg']) }}>
    <img
        src="{{ asset('images/logo.png') }}"
        alt="Estandarte Dental Clinic"
        class="w-full h-full object-cover"
    >
</div>
