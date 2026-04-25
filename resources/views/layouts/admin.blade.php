<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estandarte Dental — Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'DM Sans', sans-serif; } </style>
</head>

<body class="bg-gray-50 min-h-screen">
<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside class="w-60 bg-gray-900 text-white flex flex-col fixed top-0 left-0 h-full z-30">

        {{-- Logo --}}
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

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5">

            <a href="/dashboard"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
               {{ request()->is('dashboard') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" stroke-width="2" rx="1"/>
                    <rect x="14" y="3" width="7" height="7" stroke-width="2" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" stroke-width="2" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" stroke-width="2" rx="1"/>
                </svg>
                Overview
            </a>

            <a href="#"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-400 hover:bg-gray-800 hover:text-white transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/>
                    <line x1="16" y1="2" x2="16" y2="6" stroke-width="2"/>
                    <line x1="8" y1="2" x2="8" y2="6" stroke-width="2"/>
                    <line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/>
                </svg>
                Appointments
            </a>

            <a href="#"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-400 hover:bg-gray-800 hover:text-white transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Clinical Notes
            </a>

            <a href="#"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-400 hover:bg-gray-800 hover:text-white transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="2"/>
                    <line x1="2" y1="10" x2="22" y2="10" stroke-width="2"/>
                </svg>
                Billing
            </a>

            <a href="#"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-400 hover:bg-gray-800 hover:text-white transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <circle cx="12" cy="12" r="3" stroke-width="2"/>
                </svg>
                System Admin
            </a>

        </nav>

        {{-- Profile Settings --}}
        <div class="px-3 py-4 border-t border-gray-800">
            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-400 hover:bg-gray-800 hover:text-white transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profile Settings
            </a>
        </div>

    </aside>

    {{-- MAIN --}}
    <div class="flex-1 ml-60 flex flex-col min-h-screen">

        {{-- TOP BAR --}}
        <header class="bg-gray-900 text-white px-8 py-4 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <button class="text-gray-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="ml-2">
                    <p class="text-base font-bold leading-tight">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-yellow-400">Clinic Owner – General Dentistry</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm font-semibold">{{ now()->format('l') }}</p>
                    <p class="text-xs text-gray-400">{{ now()->format('M j, Y') }}</p>
                </div>
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit"
                            class="text-xs text-gray-400 hover:text-red-400 border border-gray-700 hover:border-red-400 px-3 py-1.5 rounded-lg transition-all">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="flex-1 p-6 overflow-y-auto">
            @yield('content')
        </main>

    </div>

</div>

@stack('scripts')
</body>
</html>
