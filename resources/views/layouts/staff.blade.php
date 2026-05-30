<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Portal — Estandarte Dental Clinic</title>
    <x-clinic-favicon />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'DM Sans', sans-serif; } </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <header class="bg-gray-900 text-white px-6 py-3.5 flex items-center justify-between sticky top-0 z-30 border-b border-yellow-500/20">
        <div class="flex items-center gap-3">
            <x-clinic-logo size="md" />
            <div>
                <p class="text-base font-bold leading-tight">Welcome, {{ auth()->user()->name ?? 'Staff' }}!</p>
                <p class="text-xs text-yellow-400">Staff Portal - Estandarte Dental Clinic</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <div class="flex items-center gap-3">
                @auth
                    <livewire:notification-bell />
                @endauth
                <button type="submit"
                class="flex items-center gap-2 text-sm font-medium text-white border border-gray-600 hover:border-yellow-400 hover:text-yellow-400 px-4 py-2 rounded-xl transition-all">
                Logout
            </button>
            </div>
        </form>
    </header>

    <main class="max-w-5xl mx-auto px-6 py-6">
        {{ $slot }}
    </main>

    @livewireScripts
    <x-ui.toast-host />
    @stack('scripts')
</body>
</html>
