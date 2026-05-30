<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — Estandarte Dental Clinic</title>
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
        <h1 class="font-display text-xl font-bold text-gray-900 mb-1 text-center">Reset Password</h1>
        <p class="text-sm text-gray-500 mb-6 text-center">Choose a new password for your account.</p>

        @if(session('error'))
            <x-ui.alert type="error" class="mb-4">{{ session('error') }}</x-ui.alert>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div>
                <label class="block text-xs font-semibold text-gray-800 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email', $email) }}" required readonly
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-800 mb-1.5">New Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 bg-[#FAFAFA] border border-gray-200 rounded-xl text-sm outline-none focus:border-yellow-500">
                @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-800 mb-1.5">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-4 py-3 bg-[#FAFAFA] border border-gray-200 rounded-xl text-sm outline-none focus:border-yellow-500">
            </div>
            <button type="submit" class="w-full py-3 rounded-full text-white text-sm font-semibold btn-gold">Reset Password</button>
        </form>
    </div>
</body>
</html>
