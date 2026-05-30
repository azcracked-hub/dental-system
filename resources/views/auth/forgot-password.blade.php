<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — Estandarte Dental Clinic</title>
    <x-clinic-favicon />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        .btn-gold { background: linear-gradient(135deg, #E2C97E 0%, #A6852E 100%); }
    </style>
</head>
<body class="min-h-screen bg-[#F0EDE8] flex items-center justify-center p-6">
    <div class="bg-white rounded-2xl w-full max-w-[460px] px-9 py-10 border border-yellow-200/30 shadow-xl">
        <x-clinic-logo size="lg" class="mx-auto mb-5" />
        <h1 class="font-display text-xl font-bold text-gray-900 mb-1 text-center">Forgot Password</h1>
        <p class="text-sm text-gray-500 mb-6 text-center">Enter your email and we'll send a reset link via SMTP.</p>

        @if(session('success'))
            <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
        @endif
        @if(session('error'))
            <x-ui.alert type="error" class="mb-4">{{ session('error') }}</x-ui.alert>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-gray-800 mb-1.5">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-3 bg-[#FAFAFA] border border-gray-200 rounded-xl text-sm outline-none focus:border-yellow-500">
                @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="w-full py-3 rounded-full text-white text-sm font-semibold btn-gold">Send Reset Link</button>
        </form>

        <p class="text-center mt-5 text-sm text-gray-500">
            <a href="{{ route('login') }}" class="text-yellow-700 font-medium hover:underline">Back to login</a>
        </p>
    </div>
</body>
</html>
