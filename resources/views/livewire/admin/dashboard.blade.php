<div>
    <x-ui.flash />

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 border-l-4 border-l-yellow-400">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-600">Today's Appointments</p>
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"/>
                    <line x1="16" y1="2" x2="16" y2="6" stroke-width="2"/>
                    <line x1="8" y1="2" x2="8" y2="6" stroke-width="2"/>
                    <line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">{{ $todayCount }}</h2>
            <p class="text-xs text-gray-400">{{ $completed }} completed</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 border-l-4 border-l-blue-400">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-600">Pending Appointments</p>
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                    <line x1="12" y1="8" x2="12" y2="12" stroke-width="2"/>
                    <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">{{ $pending }}</h2>
            <p class="text-xs text-gray-400">Awaiting confirmation</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 border-l-4 border-l-green-400">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <polyline points="17 6 23 6 23 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">₱{{ number_format($totalRevenue, 0) }}</h2>
            <p class="text-xs text-gray-400">Paid invoices</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 border-l-4 border-l-yellow-400">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-600">Pending Payments</p>
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <line x1="12" y1="1" x2="12" y2="23" stroke-width="2" stroke-linecap="round"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">₱{{ number_format($pendingPayments, 0) }}</h2>
            <p class="text-xs text-gray-400">Awaiting payment</p>
        </div>
    </div>

    {{-- Chart + Today's Schedule --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-900 mb-1">Appointment Status Distribution</h2>
            <p class="text-sm text-gray-400 mb-4">Overview of all appointments by status</p>
            <div class="flex justify-center" wire:ignore>
                <canvas id="pieChart" style="max-height: 280px;"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                    <polyline points="12 6 12 12 16 14" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <h2 class="text-base font-bold text-gray-900">Today's Schedule</h2>
            </div>
            <p class="text-sm text-gray-400 mb-5">{{ now()->format('l, F j, Y') }}</p>

            @forelse($todayAppointments as $appt)
                <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-yellow-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $appt->patient->name ?? 'Patient' }}</p>
                            <p class="text-xs text-gray-400">{{ $appt->time }}</p>
                        </div>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium
                        @if($appt->status === 'confirmed') bg-green-100 text-green-700
                        @elseif($appt->status === 'pending') bg-yellow-100 text-yellow-700
                        @elseif($appt->status === 'completed') bg-blue-100 text-blue-700
                        @else bg-red-100 text-red-700 @endif">
                        {{ ucfirst($appt->status) }}
                    </span>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="1.5"/>
                        <line x1="16" y1="2" x2="16" y2="6" stroke-width="1.5"/>
                        <line x1="8" y1="2" x2="8" y2="6" stroke-width="1.5"/>
                        <line x1="3" y1="10" x2="21" y2="10" stroke-width="1.5"/>
                    </svg>
                    <p class="text-sm">No appointments scheduled for today</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Clinical Notes --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <h2 class="text-base font-bold text-gray-900 mb-1">Recent Clinical Notes</h2>
        <p class="text-sm text-gray-400 mb-5">Latest patient notes and treatment records</p>

        @forelse($recentNotes as $note)
            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl mb-3 last:mb-0">
                <div class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800 mb-0.5">{{ $note->treatment_type ?? 'Clinical Note' }}</p>
                    <p class="text-sm text-gray-500 mb-1">{{ \Illuminate\Support\Str::limit($note->notes, 100) }}</p>
                    <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($note->created_at)->format('M j, Y') }}</p>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                <svg class="w-10 h-10 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm">No notes available</p>
            </div>
        @endforelse
    </div>

    @script
    <script>
        window.initAdminDashboardChart = function () {
            const canvas = document.getElementById('pieChart');
            if (!canvas || !window.Chart) {
                return;
            }

            const confirmed = @js($confirmed);
            const pending = @js($pending);
            const completed = @js($completed);
            const canceled = @js($canceled);
            const total = confirmed + pending + completed + canceled;

            if (window.adminDashboardChart) {
                window.adminDashboardChart.destroy();
            }

            window.adminDashboardChart = new window.Chart(canvas, {
                type: 'pie',
                data: {
                    labels: ['Confirmed', 'Pending', 'Completed', 'Canceled'],
                    datasets: [{
                        data: [confirmed, pending, completed, canceled],
                        backgroundColor: ['#10B981', '#F59E0B', '#3B82F6', '#EF4444'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyleWidth: 10,
                                font: { size: 12 },
                            },
                        },
                        tooltip: {
                            callbacks: {
                                label(context) {
                                    const val = context.parsed;
                                    const pct = total > 0 ? Math.round((val / total) * 100) : 0;
                                    return ` ${context.label}: ${pct}%`;
                                },
                            },
                        },
                    },
                },
            });
        };

        window.initAdminDashboardChart();
    </script>
    @endscript
</div>
