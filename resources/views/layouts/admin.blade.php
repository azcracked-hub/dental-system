<!DOCTYPE html>
<html>
<head>
    <title>Dental Clinic System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">

<div class="flex">

    <!-- SIDEBAR -->
    <aside class="w-64 h-screen bg-gray-900 text-white p-6">
        <h2 class="text-xl font-bold mb-6">Dental System</h2>

        <ul class="space-y-4">
            <li><a href="/dashboard" class="hover:text-blue-400">Overview</a></li>
            <li><a href="#" class="hover:text-blue-400">Appointments</a></li>
            <li><a href="#" class="hover:text-blue-400">Clinical Notes</a></li>
            <li><a href="#" class="hover:text-blue-400">Billing</a></li>
        </ul>
    </aside>

    <!-- MAIN -->
    <div class="flex-1">

        <!-- TOP BAR -->
        <div class="bg-white p-4 shadow flex justify-between items-center">

            <div>
                <h2 class="font-bold text-lg">Dental Clinic Appointment System</h2>
            </div>

            <!-- USER PANEL -->
            <div class="text-right">
                <p class="font-semibold">{{ auth()->user()->name }}</p>
                <p id="datetime" class="text-sm text-gray-500"></p>
            </div>

        </div>

        <!-- CONTENT -->
        <main class="p-6">
            @yield('content')
        </main>

    </div>

</div>

<!-- REALTIME CLOCK -->
<script>
function updateClock() {
    const now = new Date();
    document.getElementById('datetime').innerText =
        now.toLocaleDateString() + ' ' + now.toLocaleTimeString();
}
setInterval(updateClock, 1000);
updateClock();
</script>

</body>
</html>