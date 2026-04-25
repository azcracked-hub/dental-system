<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Estandarte Dental Clinic</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold: {
                            DEFAULT: '#C9A84C',
                            light:   '#E2C97E',
                            dark:    '#A6852E',
                            tab:     '#F5E9C8',
                        }
                    },
                    fontFamily: {
                        display: ['"Playfair Display"', 'serif'],
                        body:    ['"DM Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        .btn-gold {
            background: linear-gradient(135deg, #E2C97E 0%, #A6852E 100%);
            box-shadow: 0 4px 18px rgba(166,133,46,0.35);
            transition: opacity .2s, transform .15s, box-shadow .2s;
        }
        .btn-gold:hover { opacity: .92; transform: translateY(-1px); box-shadow: 0 6px 22px rgba(166,133,46,.45); }
        .btn-gold:active { transform: translateY(0); opacity: 1; }
        input:focus { border-color: #C9A84C !important; box-shadow: 0 0 0 3px rgba(201,168,76,.15) !important; }
    </style>
</head>
<body class="min-h-screen bg-[#F0EDE8] flex items-center justify-center p-6"
      style="background-image: radial-gradient(ellipse at 20% 80%, rgba(201,168,76,.08) 0%, transparent 60%), radial-gradient(ellipse at 80% 10%, rgba(201,168,76,.06) 0%, transparent 50%);">

    <div class="bg-white rounded-2xl w-full max-w-[460px] px-9 py-10 text-center border border-gold/10"
         style="box-shadow: 0 8px 40px rgba(0,0,0,.10), 0 1px 0 rgba(201,168,76,.15);">

        {{-- Logo --}}
        <div class="w-[100px] h-[100px] mx-auto mb-5 rounded-[14px] overflow-hidden shadow-lg">
            {{-- <img src="{{ asset('images/logo.png') }}" alt="Estandarte Dental Clinic Logo" class="w-full h-full object-cover"> --}}
            <div class="w-full h-full bg-[#0d0d0d] flex items-center justify-center">
                <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-16 h-16">
                    <path d="M32 8C22 8 14 17 14 26c0 5 2 9 4 13l4 14c1 3 3 3 4 0l2-7c1-3 2-3 4 0l2 7c1 3 3 3 4 0l4-14c2-4 4-8 4-13 0-9-8-18-18-18z" fill="#C9A84C"/>
                    <path d="M26 24c0-3 2-5 6-5s6 2 6 5" stroke="#0d0d0d" stroke-width="1.5" stroke-linecap="round"/>
                    <text x="32" y="58" text-anchor="middle" font-family="serif" font-size="7" fill="#C9A84C" font-style="italic">Estandarte</text>
                </svg>
            </div>
        </div>

        {{-- Heading --}}
        <h1 class="font-display text-[1.65rem] font-bold text-gray-900 tracking-tight mb-1">
            Estandarte Dental Clinic
        </h1>
        <p class="text-sm text-gray-500 mb-6 tracking-wide">Appointment &amp; Billing Portal</p>

        {{-- Tab switcher --}}
        <div class="flex bg-gold-tab rounded-full p-1 mb-7 gap-1">
            <span class="flex-1 py-2.5 rounded-full text-sm font-semibold text-white text-center btn-gold cursor-default">
                Login
            </span>
            <a href="/register"
               class="flex-1 py-2.5 rounded-full text-sm font-semibold text-gray-800 hover:bg-gold/10 transition-all text-center">
                Register
            </a>
        </div>

        {{-- Success message (after registration) --}}
        @if(session('success'))
        <div class="mb-5 bg-green-50 border border-green-200 text-green-700 text-xs rounded-lg px-4 py-3 text-left flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        {{-- Error message --}}
        @if(session('error'))
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg px-4 py-3 text-left">
            {{ session('error') }}
        </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="/login">
            @csrf

            {{-- Email --}}
            <div class="text-left mb-4">
                <label for="email" class="block text-xs font-semibold text-gray-800 mb-1.5">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       placeholder="your.email@example.com"
                       value="{{ old('email') }}"
                       class="w-full px-4 py-3 bg-[#FAFAFA] border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-300 outline-none transition-all"
                       required>
            </div>

            {{-- Password --}}
            <div class="text-left mb-5">
                <label for="password" class="block text-xs font-semibold text-gray-800 mb-1.5">Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="••••••••"
                       class="w-full px-4 py-3 bg-[#FAFAFA] border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-300 outline-none transition-all"
                       required>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full py-3.5 rounded-full text-white text-sm font-semibold tracking-wide btn-gold border-0 cursor-pointer">
                Sign In
            </button>
        </form>

        {{-- Demo accounts --}}
        <div class="mt-6 bg-[#FAFAF7] border border-gray-200 rounded-xl px-4 py-3.5 text-left">
            <p class="text-xs text-gray-400 mb-2">Demo Accounts:</p>
            <p class="text-xs mb-1">
                <span class="font-bold text-[#B08800]">Patient:</span>
                <span class="text-gray-500"> john.doe@email.com / patient123</span>
            </p>
            <p class="text-xs mb-1">
                <span class="font-bold text-[#7B6000]">Doctor:</span>
                <span class="text-gray-500"> dr.estandarte@dentalclinic.com / doctor123</span>
            </p>
            <p class="text-xs">
                <span class="font-bold text-[#A67C00]">Staff:</span>
                <span class="text-gray-500"> anna.reyes@dentalclinic.com / staff123</span>
            </p>
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>
