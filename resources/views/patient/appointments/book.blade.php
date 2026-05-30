@extends('layouts.patient')

@section('content')

<x-ui.flash />

<div id="service-limit-notice" class="hidden mb-4 px-4 py-3 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-sm flex items-center gap-2" role="alert">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <span>You can select up to 3 services only.</span>
</div>

{{-- Back + Header --}}
<div class="mb-6">
    <a href="{{ route('patient.dashboard') }}"
       class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 mb-4 transition-all w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Dashboard
    </a>
    <h1 class="text-2xl font-bold text-gray-900 mb-1">Book Appointment</h1>
    <p class="text-sm text-gray-400">Select service → Choose doctor → Pick date & time</p>
</div>

{{-- Stepper --}}
<div class="flex items-center mb-6 px-2">
    <div class="flex items-center gap-2">
        <div id="step-circle-1"
             class="w-8 h-8 rounded-full bg-blue-600 text-white text-sm font-bold flex items-center justify-center">1</div>
        <span id="step-label-1" class="text-sm font-semibold text-gray-800">Service</span>
    </div>
    <div id="line-1-2" class="flex-1 h-0.5 mx-3 bg-gray-200"></div>
    <div class="flex items-center gap-2">
        <div id="step-circle-2"
             class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 text-sm font-bold flex items-center justify-center">2</div>
        <span id="step-label-2" class="text-sm font-medium text-gray-400">Doctor</span>
    </div>
    <div id="line-2-3" class="flex-1 h-0.5 mx-3 bg-gray-200"></div>
    <div class="flex items-center gap-2">
        <div id="step-circle-3"
             class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 text-sm font-bold flex items-center justify-center">3</div>
        <span id="step-label-3" class="text-sm font-medium text-gray-400">Date & Time</span>
    </div>
</div>

{{-- STEP 1: Select Service --}}
<div id="step-1" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <h2 class="text-base font-bold text-gray-900 mb-1">Select Services</h2>
    <p class="text-sm text-gray-400 mb-2">Choose 1 to 3 dental services — you are not required to pick all three.</p>
    <p id="service-count-label" class="text-xs font-semibold text-blue-600 mb-4">0 selected — pick at least 1 to continue</p>

    <div class="space-y-2">
        @foreach($services as $service)
            <label class="service-item flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all">
                <input type="checkbox" value="{{ $service->id }}"
                    class="service-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 shrink-0"
                    data-name="{{ $service->name }}"
                    data-price="{{ $service->price }}">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900">{{ $service->name }}</p>
                    <p class="text-xs text-gray-400">{{ $service->duration_minutes }} minutes</p>
                </div>
                <span class="text-sm font-bold text-blue-600 shrink-0">₱{{ number_format($service->price, 0) }}</span>
            </label>
        @endforeach
    </div>

    <div class="mt-5">
        <button onclick="goToStep(2)" id="btn-step1-continue" disabled
                class="w-full py-3 rounded-xl text-sm font-semibold bg-blue-200 text-blue-400 cursor-not-allowed transition-all">
            Continue
        </button>
    </div>
</div>

{{-- STEP 2: Select Doctor --}}
<div id="step-2" class="hidden bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <h2 class="text-base font-bold text-gray-900 mb-1">Select Doctor</h2>
    <p class="text-sm text-gray-400 mb-5">Choose your preferred dentist</p>

    <div class="mb-4">
        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Search by Last Name</label>
        <input type="text" id="doctor-search"
               placeholder="Enter doctor's last name..."
               oninput="filterDoctors(this.value)"
               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-300 outline-none focus:border-blue-400 transition-all">
    </div>

    <div class="space-y-2" id="doctor-list">
        @foreach($doctors as $doctor)
            @php $lastName = strtolower(explode(' ', trim($doctor->name))[count(explode(' ', trim($doctor->name)))-1]); @endphp
            <div class="doctor-item flex items-center gap-4 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all"
                 data-id="{{ $doctor->id }}"
                 data-name="{{ $doctor->name }}"
                 data-lastname="{{ $lastName }}"
                 onclick="selectDoctor(this)">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">{{ $doctor->name }}</p>
                    <p class="text-xs text-gray-500">{{ $doctor->specialization ?? 'General Dentistry' }}</p>
                    <p class="text-xs text-gray-400">License: {{ $doctor->license_number ?? 'PRC-12345' }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="flex gap-3 mt-5">
        <button onclick="goToStep(1)"
                class="flex-1 py-3 rounded-xl text-sm font-semibold border border-gray-200 hover:bg-gray-50 text-gray-700 transition-all">
            Back
        </button>
        <button onclick="goToStep(3)" id="btn-step2-continue" disabled
                class="flex-1 py-3 rounded-xl text-sm font-semibold bg-blue-200 text-blue-400 cursor-not-allowed transition-all">
            Continue
        </button>
    </div>
</div>

{{-- STEP 3: Date & Time --}}
<div id="step-3" class="hidden bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <h2 class="text-base font-bold text-gray-900 mb-1">Select Date & Time</h2>
    <p class="text-sm text-gray-400 mb-5">Choose your appointment slot</p>

    <p class="text-xs font-semibold text-gray-700 mb-3">Select Date</p>

    {{-- Calendar --}}
    <div class="flex justify-center mb-5">
        <div class="border border-gray-200 rounded-xl p-4 w-80">
            <div class="flex items-center justify-between mb-3">
                <button type="button" onclick="prevMonth()"
                        class="text-gray-400 hover:text-gray-700 p-1 rounded transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <span class="text-sm font-bold text-gray-800" id="cal-month-label"></span>
                <button type="button" onclick="nextMonth()"
                        class="text-gray-400 hover:text-gray-700 p-1 rounded transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-7 text-center mb-2">
                @foreach(['Su','Mo','Tu','We','Th','Fr','Sa'] as $d)
                    <div class="text-xs font-medium text-gray-400 py-1">{{ $d }}</div>
                @endforeach
            </div>
            <div class="grid grid-cols-7 text-center" id="cal-days"></div>
        </div>
    </div>

    {{-- Time Slots --}}
    <div id="timeslot-section" class="hidden">
        <p class="text-xs font-semibold text-gray-700 mb-3">Available Time Slots</p>
        <div class="grid grid-cols-3 gap-2" id="timeslot-grid"></div>
    </div>

    <div class="flex gap-3 mt-5">
        <button type="button" onclick="goToStep(2)"
                class="flex-1 py-3 rounded-xl text-sm font-semibold border border-gray-200 hover:bg-gray-50 text-gray-700 transition-all">
            Back
        </button>
        <form method="POST" action="{{ route('patient.appointments.store') }}" class="flex-1" id="booking-form">
            @csrf
            <div id="service-ids-container"></div>
            <input type="hidden" name="doctor_id" id="input-doctor-id">
            <input type="hidden" name="date" id="input-date">
            <input type="hidden" name="time" id="input-time">
            <button type="submit" id="btn-confirm" disabled
                    class="w-full py-3 rounded-xl text-sm font-semibold bg-blue-200 text-blue-400 cursor-not-allowed transition-all">
                Confirm Booking
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
let selectedServices = [];
let selectedDoctor  = null;
let selectedDate    = null;
let selectedTime    = null;
let calYear, calMonth;

const timeSlots = [
    '08:00','08:30','09:00','09:30','10:00','10:30',
    '13:00','13:30','14:00','14:30','15:00','15:30',
    '16:00','16:30','17:00'
];

function updateServiceSelectionUI() {
    const count = selectedServices.length;
    const label = document.getElementById('service-count-label');
    if (label) {
        label.textContent = count === 0
            ? '0 selected — pick at least 1 to continue'
            : count + ' service' + (count > 1 ? 's' : '') + ' selected (max 3)';
    }

    document.querySelectorAll('.service-item').forEach(item => {
        const checkbox = item.querySelector('.service-checkbox');
        const isSelected = checkbox && checkbox.checked;
        item.classList.toggle('border-blue-500', isSelected);
        item.classList.toggle('bg-blue-50', isSelected);
        item.classList.toggle('border-gray-200', !isSelected);
    });

    const container = document.getElementById('service-ids-container');
    container.innerHTML = '';
    selectedServices.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'service_ids[]';
        input.value = id;
        container.appendChild(input);
    });

    const btn = document.getElementById('btn-step1-continue');
    if (count > 0) {
        btn.disabled = false;
        btn.className = 'w-full py-3 rounded-xl text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white cursor-pointer transition-all';
    } else {
        btn.disabled = true;
        btn.className = 'w-full py-3 rounded-xl text-sm font-semibold bg-blue-200 text-blue-400 cursor-not-allowed transition-all';
    }
}

function showServiceLimitNotice() {
    const notice = document.getElementById('service-limit-notice');
    if (!notice) return;
    notice.classList.remove('hidden');
    clearTimeout(window._serviceLimitTimer);
    window._serviceLimitTimer = setTimeout(() => notice.classList.add('hidden'), 4000);
}

document.querySelectorAll('.service-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
        const id = this.value;
        if (this.checked) {
            if (selectedServices.length >= 3) {
                this.checked = false;
                showServiceLimitNotice();
                return;
            }
            if (!selectedServices.includes(id)) {
                selectedServices.push(id);
            }
        } else {
            selectedServices = selectedServices.filter(s => s !== id);
        }
        updateServiceSelectionUI();
    });
});

// ── Doctor selection ─────────────────────────────────────────
function selectDoctor(el) {
    document.querySelectorAll('.doctor-item').forEach(i => {
        i.classList.remove('border-blue-500', 'bg-blue-50');
        i.classList.add('border-gray-200');
    });
    el.classList.add('border-blue-500', 'bg-blue-50');
    el.classList.remove('border-gray-200');
    selectedDoctor = { id: el.dataset.id, name: el.dataset.name };
    document.getElementById('input-doctor-id').value = el.dataset.id;

    const btn = document.getElementById('btn-step2-continue');
    btn.disabled = false;
    btn.className = 'flex-1 py-3 rounded-xl text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white cursor-pointer transition-all';
}

function filterDoctors(val) {
    document.querySelectorAll('.doctor-item').forEach(el => {
        el.style.display = el.dataset.lastname.includes(val.toLowerCase()) ? '' : 'none';
    });
}

// ── Step navigation ──────────────────────────────────────────
function goToStep(step) {
    [1, 2, 3].forEach(s => {
        document.getElementById('step-' + s).classList.add('hidden');
    });
    document.getElementById('step-' + step).classList.remove('hidden');

    // Update stepper UI
    [1, 2, 3].forEach(s => {
        const circle = document.getElementById('step-circle-' + s);
        const label  = document.getElementById('step-label-' + s);
        if (s <= step) {
            circle.className = 'w-8 h-8 rounded-full bg-blue-600 text-white text-sm font-bold flex items-center justify-center';
            label.className  = 'text-sm font-semibold text-gray-800';
        } else {
            circle.className = 'w-8 h-8 rounded-full bg-gray-200 text-gray-500 text-sm font-bold flex items-center justify-center';
            label.className  = 'text-sm font-medium text-gray-400';
        }
        if (s < 3) {
            const line = document.getElementById('line-' + s + '-' + (s + 1));
            line.className = s < step
                ? 'flex-1 h-0.5 mx-3 bg-blue-600'
                : 'flex-1 h-0.5 mx-3 bg-gray-200';
        }
    });

    if (step === 3) initCalendar();
}

// ── Calendar ─────────────────────────────────────────────────
function initCalendar() {
    if (!calYear) {
        const now = new Date();
        calYear  = now.getFullYear();
        calMonth = now.getMonth();
    }
    renderCalendar();
}

function renderCalendar() {
    const months = ['January','February','March','April','May','June',
                    'July','August','September','October','November','December'];
    document.getElementById('cal-month-label').textContent = months[calMonth] + ' ' + calYear;

    const grid  = document.getElementById('cal-days');
    grid.innerHTML = '';

    const firstDay = new Date(calYear, calMonth, 1).getDay();
    const daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();
    const today = new Date(); today.setHours(0, 0, 0, 0);

    // Prev month filler
    for (let i = 0; i < firstDay; i++) {
        const d = new Date(calYear, calMonth, 0 - (firstDay - i - 1));
        grid.innerHTML += `<div class="text-xs text-gray-300 py-1.5 text-center">${d.getDate()}</div>`;
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const date    = new Date(calYear, calMonth, d);
        const isPast  = date < today;
        const isToday = date.toDateString() === today.toDateString();
        const dateStr = calYear + '-' + String(calMonth + 1).padStart(2, '0') + '-' + String(d).padStart(2, '0');
        const isSel   = selectedDate === dateStr;

        let cls = 'text-xs py-1.5 text-center rounded-lg transition-all ';
        if (isPast)     cls += 'text-gray-300 cursor-not-allowed';
        else if (isSel) cls += 'bg-yellow-500 text-white font-bold cursor-pointer';
        else if (isToday) cls += 'bg-yellow-100 text-yellow-700 font-semibold hover:bg-yellow-200 cursor-pointer';
        else            cls += 'text-gray-700 hover:bg-gray-100 cursor-pointer';

        const click = !isPast ? `onclick="selectDate('${dateStr}')"` : '';
        grid.innerHTML += `<div class="${cls}" ${click}>${d}</div>`;
    }

    // Next month filler
    const total     = firstDay + daysInMonth;
    const remaining = total % 7 === 0 ? 0 : 7 - (total % 7);
    for (let i = 1; i <= remaining; i++) {
        grid.innerHTML += `<div class="text-xs text-gray-300 py-1.5 text-center">${i}</div>`;
    }
}

function prevMonth() {
    calMonth--;
    if (calMonth < 0) { calMonth = 11; calYear--; }
    renderCalendar();
}

function nextMonth() {
    calMonth++;
    if (calMonth > 11) { calMonth = 0; calYear++; }
    renderCalendar();
}

function selectDate(date) {
    selectedDate = date;
    selectedTime = null;
    document.getElementById('input-date').value = date;
    renderCalendar();

    // Build time slots
    const grid = document.getElementById('timeslot-grid');
    grid.innerHTML = '';
    timeSlots.forEach(t => {
        grid.innerHTML += `
            <button type="button" onclick="selectTime(this,'${t}')"
                    class="timeslot-btn flex items-center justify-center gap-1.5 px-3 py-2.5
                           border border-gray-200 rounded-xl text-xs font-medium text-gray-700
                           hover:border-blue-400 hover:bg-blue-50 transition-all">
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                    <polyline points="12 6 12 12 16 14" stroke-width="2" stroke-linecap="round"/>
                </svg>
                ${t}
            </button>`;
    });
    document.getElementById('timeslot-section').classList.remove('hidden');
    updateConfirmBtn();
}

function selectTime(el, time) {
    document.querySelectorAll('.timeslot-btn').forEach(b => {
        b.classList.remove('border-yellow-500', 'bg-yellow-50', 'text-yellow-700');
        b.classList.add('border-gray-200', 'text-gray-700');
    });
    el.classList.add('border-yellow-500', 'bg-yellow-50', 'text-yellow-700');
    el.classList.remove('border-gray-200', 'text-gray-700');
    selectedTime = time;
    document.getElementById('input-time').value = time;
    updateConfirmBtn();
}

function updateConfirmBtn() {
    const btn = document.getElementById('btn-confirm');
    if (selectedDate && selectedTime) {
        btn.disabled = false;
        btn.className = 'w-full py-3 rounded-xl text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white cursor-pointer transition-all';
    } else {
        btn.disabled = true;
        btn.className = 'w-full py-3 rounded-xl text-sm font-semibold bg-blue-200 text-blue-400 cursor-not-allowed transition-all';
    }
}
</script>
@endpush
