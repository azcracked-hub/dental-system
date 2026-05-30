<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estandarte Dental — Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'DM Sans', sans-serif; } </style>
</head>

<body class="bg-gray-50 min-h-screen">
<div class="flex min-h-screen">

    {{-- SIDEBAR — wire:navigate for SPA; wire:current for active state --}}
    <aside class="w-60 bg-gray-900 text-white flex flex-col fixed top-0 left-0 h-full z-30">

        <div class="px-5 py-5 border-b border-gray-800 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-[#0d0d0d] border border-yellow-500/30 flex items-center justify-center shrink-0">
                <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6">
                    <path d="M32 8C22 8 14 17 14 26c0 5 2 9 4 13l4 14c1 3 3 3 4 0l2-7c1-3 2-3 4 0l2 7c1 3 3 3 4 0l4-14c2-4 4-8 4-13 0-9-8-18-18-18z" fill="#C9A84C"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold text-white leading-tight">Estandarte Dental</p>
                <p class="text-xs text-yellow-400">{{ auth()->user()->name ?? '' }}</p>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-0.5">
            @php
                $navClass = 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all data-current:bg-gray-800 data-current:text-white text-gray-400 hover:bg-gray-800 hover:text-white';
            @endphp

            <a href="{{ route('admin.dashboard') }}" wire:navigate.hover class="{{ $navClass }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" stroke-width="2" rx="1"/>
                    <rect x="14" y="3" width="7" height="7" stroke-width="2" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" stroke-width="2" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" stroke-width="2" rx="1"/>
                </svg>
                Overview
            </a>

            <a href="{{ route('admin.appointments.index') }}" wire:navigate.hover class="{{ $navClass }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/>
                    <line x1="16" y1="2" x2="16" y2="6" stroke-width="2"/>
                    <line x1="8" y1="2" x2="8" y2="6" stroke-width="2"/>
                    <line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/>
                </svg>
                Appointments
            </a>

            <a href="{{ route('admin.clinical-notes.index') }}" wire:navigate.hover class="{{ $navClass }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Clinical Notes
            </a>

            <a href="{{ route('admin.billing.index') }}" wire:navigate.hover class="{{ $navClass }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="2"/>
                    <line x1="2" y1="10" x2="22" y2="10" stroke-width="2"/>
                </svg>
                Billing
            </a>

            <a href="{{ route('admin.patients.index') }}" wire:navigate.hover class="{{ $navClass }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Patients
            </a>

            <a href="{{ route('admin.system-admin.index') }}" wire:navigate.hover class="{{ $navClass }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <circle cx="12" cy="12" r="3" stroke-width="2"/>
                </svg>
                System Admin
            </a>
        </nav>

        <div class="px-3 py-4 border-t border-gray-800 space-y-0.5">
            <button type="button" onclick="document.getElementById('profileModal').classList.remove('hidden')"
                class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-sm font-medium text-gray-400 hover:bg-gray-800 hover:text-white transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profile Settings
            </button>

            <form method="POST" action="/logout">
                @csrf
                <button type="submit"
                    class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-sm font-medium text-gray-400 hover:bg-red-900/30 hover:text-red-400 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 ml-60 flex flex-col min-h-screen">
        <header class="bg-gray-900 text-white px-8 py-4 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-yellow-400/10 border border-yellow-400/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold leading-tight">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-yellow-400">
                        Clinic Owner
                        @if(auth()->user()->specialization)
                            — {{ auth()->user()->specialization }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold">{{ now()->format('l') }}</p>
                <p class="text-xs text-gray-400">{{ now()->format('M j, Y') }}</p>
            </div>
        </header>

        {{-- No overflow-y-auto here — prevents modal clipping --}}
        <main class="flex-1 p-6">
            {{ $slot }}
        </main>
    </div>
</div>

{{-- Profile modal lives outside main/sidebar so wire:navigate never destroys it --}}
<div id="profileModal" wire:ignore class="hidden fixed inset-0 bg-black/40 z-[200] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 relative z-[201]">
        <div class="flex items-center justify-between mb-1">
            <div>
                <h2 class="text-base font-bold text-gray-900">Profile Settings</h2>
                <p class="text-sm text-gray-400">Update your personal information and credentials</p>
            </div>
            <button type="button" onclick="document.getElementById('profileModal').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
        </div>
        <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-4 mt-4">
            @csrf @method('PATCH')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">First Name</label>
                    <input type="text" name="first_name"
                        value="{{ explode(' ', auth()->user()->name)[0] ?? '' }}"
                        class="w-full border border-yellow-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">Last Name</label>
                    <input type="text" name="last_name"
                        value="{{ explode(' ', auth()->user()->name, 2)[1] ?? '' }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
                </div>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Email</label>
                <input type="email" name="email" value="{{ auth()->user()->email }}"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Phone Number</label>
                <input type="text" name="phone" value="{{ auth()->user()->phone }}"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">License Number</label>
                <input type="text" name="license_number" value="{{ auth()->user()->license_number }}"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Specialization</label>
                <input type="text" name="specialization" value="{{ auth()->user()->specialization }}"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button"
                    onclick="document.getElementById('profileModal').classList.add('hidden')"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold py-2.5 rounded-xl">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

@livewireScripts
<x-ui.toast-host />
@stack('scripts')
</body>
</html>
