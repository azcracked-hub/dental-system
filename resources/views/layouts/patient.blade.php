<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Portal — Estandarte Dental Clinic</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'DM Sans', sans-serif; } </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <header class="bg-gray-900 text-white px-6 py-3.5 flex items-center justify-between sticky top-0 z-30 border-b border-yellow-500/20">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#0d0d0d] border border-yellow-500/30 flex items-center justify-center shrink-0">
                <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6">
                    <path d="M32 8C22 8 14 17 14 26c0 5 2 9 4 13l4 14c1 3 3 3 4 0l2-7c1-3 2-3 4 0l2 7c1 3 3 3 4 0l4-14c2-4 4-8 4-13 0-9-8-18-18-18z" fill="#C9A84C"/>
                </svg>
            </div>
            <div>
                <p class="text-base font-bold leading-tight">Welcome, {{ auth()->user()->name ?? 'Patient' }}!</p>
                <p class="text-xs text-yellow-400">Patient Portal - Estandarte Dental Clinic</p>
            </div>
        </div>

        <form method="POST" action="/logout">
            @csrf
            <button type="submit"
                class="flex items-center gap-2 text-sm font-medium text-white border border-gray-600 hover:border-yellow-400 hover:text-yellow-400 px-4 py-2 rounded-xl transition-all">
                Logout
            </button>
        </form>
    </header>

    <main class="max-w-5xl mx-auto px-6 py-6">
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot }}
        @endif
    </main>

    @livewireScripts
    @stack('scripts')
</body>
</html>
