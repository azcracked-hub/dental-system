
@extends('layouts.admin')

@section('content')

<h1 class="text-2xl font-bold mb-6">Dashboard</h1>

<!-- STATISTICS CARDS -->
<div class="grid grid-cols-4 gap-4 mb-6">

    <div class="bg-white p-4 rounded shadow">
        <p>Today's Appointments</p>
        <h2 class="text-2xl font-bold">{{ $todayCount }}</h2>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <p>Pending Appointments</p>
        <h2 class="text-2xl font-bold">{{ $pending }}</h2>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <p>Total Revenue</p>
        <h2 class="text-2xl font-bold">₱{{ $totalRevenue }}</h2>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <p>Pending Payments</p>
        <h2 class="text-2xl font-bold">₱{{ $pendingPayments }}</h2>
    </div>

</div>

<!-- CHART + TODAY SCHEDULE -->
<div class="grid grid-cols-2 gap-4 mb-6">

    <!-- PIE CHART -->
    <div class="bg-white p-6 rounded shadow">
        <h2 class="mb-4 font-bold">Appointment Status</h2>
        <canvas id="pieChart"></canvas>
    </div>

    <!-- TODAY SCHEDULE -->
    <div class="bg-white p-6 rounded shadow">
        <h2 class="mb-4 font-bold">Today's Schedule</h2>

        @forelse($todayAppointments as $appt)
            <div class="border-b py-2">
                <p>{{ $appt->time }} - {{ $appt->status }}</p>
            </div>
        @empty
            <p>No appointments today</p>
        @endforelse
    </div>

</div>

<!-- RECENT CLINICAL NOTES -->
<div class="bg-white p-6 rounded shadow">
    <h2 class="mb-4 font-bold">Recent Clinical Notes</h2>

    @forelse($recentNotes as $note)
        <div class="border-b py-2">
            <p>{{ $note->notes }}</p>
        </div>
    @empty
        <p>No notes available</p>
    @endforelse
</div>

@endsection
<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('pieChart');

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Confirmed', 'Pending', 'Completed', 'Canceled'],
            datasets: [{
                data: [10, 5, 8, 2],
                backgroundColor: [
                    '#10B981',
                    '#F59E0B',
                    '#3B82F6',
                    '#EF4444'
                ]
            }]
        }
    });
});
</script>

@endsection