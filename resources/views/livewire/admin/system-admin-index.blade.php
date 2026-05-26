<div>
    <x-ui.flash />

    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">System Admin</h1>
        <p class="text-sm text-gray-400">Manage users and system overview</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 border-l-4 border-l-yellow-400">
            <p class="text-sm text-gray-500 mb-1">Total Patients</p>
            <p class="text-3xl font-bold text-gray-900">{{ $totalPatients }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 border-l-4 border-l-blue-400">
            <p class="text-sm text-gray-500 mb-1">Total Appointments</p>
            <p class="text-3xl font-bold text-gray-900">{{ $totalAppointments }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-base font-bold text-gray-900 mb-4">System Users</h2>
        <div class="space-y-3">
            @forelse($users as $user)
                <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $user->email }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400">No users found.</p>
            @endforelse
        </div>
    </div>
</div>
