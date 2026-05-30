<div>
    <x-ui.flash />

    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Clinic Schedule</h1>
        <p class="text-sm text-gray-400">Holidays, doctor unavailability, and booking rules (Mon–Sat, 8:00 AM – 4:00 PM)</p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
        {{-- Holidays --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-900 mb-4">Clinic Holidays</h2>
            <form wire:submit="addHoliday" class="space-y-3 mb-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-medium text-gray-600 block mb-1">Date</label>
                        <input type="date" wire:model="holidayDate" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                        @error('holidayDate') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600 block mb-1">Holiday name</label>
                        <input type="text" wire:model="holidayName" placeholder="e.g. Independence Day" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                        @error('holidayName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold px-4 py-2 rounded-xl">Add Holiday</button>
            </form>
            <div class="space-y-2">
                @forelse($holidays as $holiday)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $holiday->name }}</p>
                            <p class="text-xs text-gray-400">{{ $holiday->date->format('M d, Y') }}</p>
                        </div>
                        <button wire:click="removeHoliday({{ $holiday->id }})" class="text-xs text-red-500 hover:underline">Remove</button>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">No holidays set.</p>
                @endforelse
            </div>
        </div>

        {{-- Doctor unavailability --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-900 mb-1">Doctor Unavailable Dates</h2>
            <p class="text-xs text-gray-400 mb-4">Patients cannot book these days. Existing appointments will be flagged for rescheduling.</p>
            <form wire:submit="addUnavailability" class="space-y-3 mb-4">
                <div>
                    <label class="text-xs font-medium text-gray-600 block mb-1">Doctor</label>
                    <select wire:model="unavailDoctorId" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                        <option value="">Select doctor</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                        @endforeach
                    </select>
                    @error('unavailDoctorId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-medium text-gray-600 block mb-1">Date</label>
                        <input type="date" wire:model="unavailDate" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                        @error('unavailDate') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600 block mb-1">Reason (optional)</label>
                        <input type="text" wire:model="unavailReason" placeholder="Out of town" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                    </div>
                </div>
                <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold px-4 py-2 rounded-xl">Mark Unavailable</button>
            </form>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @forelse($unavailabilities as $block)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $block->doctor->name ?? 'Doctor' }}</p>
                            <p class="text-xs text-gray-400">{{ $block->date->format('M d, Y') }} @if($block->reason) — {{ $block->reason }} @endif</p>
                        </div>
                        <button wire:click="removeUnavailability({{ $block->id }})" class="text-xs text-red-500 hover:underline">Remove</button>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">No blocked dates.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-800">
        <strong>Booking rules:</strong> Monday–Saturday only • Sundays closed • Hours 8:00 AM – 4:00 PM (hourly slots) • Fully booked days are blocked automatically
    </div>
</div>
