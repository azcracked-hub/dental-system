<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Estandarte Dental Clinic</title>
    <x-clinic-favicon />
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

        <x-clinic-logo size="lg" class="mx-auto mb-5" />

        {{-- Heading --}}
        <h1 class="font-display text-[1.65rem] font-bold text-gray-900 tracking-tight mb-1">
            Estandarte Dental Clinic
        </h1>
        <p class="text-sm text-gray-500 mb-6 tracking-wide">Appointment &amp; Billing Portal</p>

        {{-- Tab switcher --}}
        <div class="flex bg-gold-tab rounded-full p-1 mb-7 gap-1">
            <a href="/login"
               class="flex-1 py-2.5 rounded-full text-sm font-semibold text-gray-800 hover:bg-gold/10 transition-all text-center">
                Login
            </a>
            <span class="flex-1 py-2.5 rounded-full text-sm font-semibold text-white text-center btn-gold cursor-default">
                Register
            </span>
        </div>

        {{-- Error --}}
        @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg px-4 py-3 text-left">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Register Form --}}
        <form method="POST" action="/register">
            @csrf

            {{-- First Name + Last Name --}}
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="text-left">
                    <label for="first_name" class="block text-xs font-semibold text-gray-800 mb-1.5">First Name</label>
                    <input type="text"
                           id="first_name"
                           name="first_name"
                           placeholder="John"
                           value="{{ old('first_name') }}"
                           class="w-full px-4 py-3 bg-[#FAFAFA] border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-300 outline-none transition-all"
                           required>
                </div>
                <div class="text-left">
                    <label for="last_name" class="block text-xs font-semibold text-gray-800 mb-1.5">Last Name</label>
                    <input type="text"
                           id="last_name"
                           name="last_name"
                           placeholder="Doe"
                           value="{{ old('last_name') }}"
                           class="w-full px-4 py-3 bg-[#FAFAFA] border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-300 outline-none transition-all"
                           required>
                </div>
            </div>

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

            {{-- Phone Number --}}
            <div class="text-left mb-4">
                <label for="phone" class="block text-xs font-semibold text-gray-800 mb-1.5">Phone Number</label>
                <input type="tel"
                       id="phone"
                       name="phone"
                       placeholder="+63 912 345 6789"
                       value="{{ old('phone') }}"
                       class="w-full px-4 py-3 bg-[#FAFAFA] border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-300 outline-none transition-all">
            </div>

            {{-- Password --}}
            <div class="text-left mb-4">
                <label for="password" class="block text-xs font-semibold text-gray-800 mb-1.5">Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="••••••••"
                       class="w-full px-4 py-3 bg-[#FAFAFA] border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-300 outline-none transition-all"
                       required>
                @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="text-left mb-5">
                <label for="password_confirmation" class="block text-xs font-semibold text-gray-800 mb-1.5">Confirm Password</label>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       placeholder="••••••••"
                       class="w-full px-4 py-3 bg-[#FAFAFA] border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-300 outline-none transition-all"
                       required>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full py-3.5 rounded-full text-white text-sm font-semibold tracking-wide btn-gold border-0 cursor-pointer">
                Create Account
            </button>
        </form>

        {{-- Footer note --}}
        <p class="mt-4 text-xs text-gray-400">New patients will be registered with patient role</p>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>
