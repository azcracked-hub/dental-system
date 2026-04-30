<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Portal — Estandarte Dental Clinic</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'DM Sans', sans-serif; } </style>
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- TOP BAR --}}
    <header class="bg-gray-900 text-white px-6 py-3.5 flex items-center justify-between sticky top-0 z-30 border-b border-yellow-500/20">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#0d0d0d] border border-yellow-500/30 flex items-center justify-center shrink-0">
                <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6">
                    <path d="M32 8C22 8 14 17 14 26c0 5 2 9 4 13l4 14c1 3 3 3 4 0l2-7c1-3 2-3 4 0l2 7c1 3 3 3 4 0l4-14c2-4 4-8 4-13 0-9-8-18-18-18z" fill="#C9A84C"/>
                </svg>
            </div>
            <div>
                <p class="text-base font-bold leading-tight">
                    Welcome, {{ auth()->user()->name ?? 'Patient' }}!
                </p>
                <p class="text-xs text-yellow-400 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    Patient Portal - Estandarte Dental Clinic
                </p>
            </div>
        </div>

        <form method="POST" action="/logout">
            @csrf
            <button type="submit"
                    class="flex items-center gap-2 text-sm font-medium text-white border border-gray-600 hover:border-yellow-400 hover:text-yellow-400 px-4 py-2 rounded-xl transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </form>
    </header>

    <main class="max-w-5xl mx-auto px-6 py-6">
        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
