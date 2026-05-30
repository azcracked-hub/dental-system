<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\ClinicHoliday;
use App\Models\DoctorUnavailability;
use App\Support\ClinicTime;
use Carbon\Carbon;

class SchedulingService
{
    public function isSunday(string $date): bool
    {
        return Carbon::parse($date)->isSunday();
    }

    public function isHoliday(string $date): bool
    {
        return ClinicHoliday::whereDate('date', $date)->exists();
    }

    public function isDoctorUnavailable(int $doctorId, string $date): bool
    {
        return DoctorUnavailability::where('doctor_id', $doctorId)
            ->whereDate('date', $date)
            ->exists();
    }

    public function getDayBlockReason(int $doctorId, string $date): ?string
    {
        if (Carbon::parse($date)->isPast() && ! Carbon::parse($date)->isToday()) {
            return 'Past date';
        }

        if ($this->isSunday($date)) {
            return 'Clinic closed on Sundays';
        }

        if ($this->isHoliday($date)) {
            $holiday = ClinicHoliday::whereDate('date', $date)->first();

            return 'Holiday: '.($holiday->name ?? 'Clinic closed');
        }

        if ($this->isDoctorUnavailable($doctorId, $date)) {
            $block = DoctorUnavailability::where('doctor_id', $doctorId)
                ->whereDate('date', $date)
                ->first();

            return 'Doctor unavailable'.($block?->reason ? ': '.$block->reason : '');
        }

        if ($this->isFullyBooked($doctorId, $date)) {
            return 'Fully booked — no time slots left';
        }

        return null;
    }

    public function isBookableDay(int $doctorId, string $date): bool
    {
        return $this->getDayBlockReason($doctorId, $date) === null;
    }

    public function isFullyBooked(int $doctorId, string $date): bool
    {
        if ($this->isSunday($date) || $this->isHoliday($date) || $this->isDoctorUnavailable($doctorId, $date)) {
            return false;
        }

        return count($this->computeAvailableSlots($doctorId, $date)) === 0;
    }

    public function getAvailableSlots(int $doctorId, string $date): array
    {
        if ($this->isSunday($date) || $this->isHoliday($date) || $this->isDoctorUnavailable($doctorId, $date)) {
            return [];
        }

        return $this->computeAvailableSlots($doctorId, $date);
    }

    /** @return array<string, string> */
    private function computeAvailableSlots(int $doctorId, string $date): array
    {
        $booked = $this->getBookedTimes($doctorId, $date);
        $today = Carbon::today();
        $selected = Carbon::parse($date);

        $slots = [];
        foreach (ClinicTime::SLOTS as $slot) {
            if ($selected->isSameDay($today)) {
                $slotTime = Carbon::parse($date.' '.$slot);
                if ($slotTime->lte(now())) {
                    continue;
                }
            }

            if (! in_array($slot, $booked, true)) {
                $slots[$slot] = ClinicTime::slotLabel($slot);
            }
        }

        return $slots;
    }

    public function getBookedTimes(int $doctorId, string $date): array
    {
        return Appointment::query()
            ->where('doctor_id', $doctorId)
            ->whereDate('date', $date)
            ->whereNotIn('status', ['canceled'])
            ->pluck('time')
            ->map(fn ($t) => substr((string) $t, 0, 5))
            ->all();
    }

    public function isSlotAvailable(int $doctorId, string $date, string $time): bool
    {
        $normalized = substr($time, 0, 5);
        $available = $this->getAvailableSlots($doctorId, $date);

        return array_key_exists($normalized, $available);
    }

    /** @return array<string, string> day => status for calendar (available|closed|holiday|unavailable|full|past) */
    public function getMonthAvailability(int $doctorId, int $year, int $month): array
    {
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        $result = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $carbon = Carbon::parse($date);

            if ($carbon->isPast() && ! $carbon->isToday()) {
                $result[$date] = 'past';
                continue;
            }

            if ($this->isSunday($date)) {
                $result[$date] = 'closed';
                continue;
            }

            if ($this->isHoliday($date)) {
                $result[$date] = 'holiday';
                continue;
            }

            if ($this->isDoctorUnavailable($doctorId, $date)) {
                $result[$date] = 'unavailable';
                continue;
            }

            if ($this->isFullyBooked($doctorId, $date)) {
                $result[$date] = 'full';
                continue;
            }

            $result[$date] = 'available';
        }

        return $result;
    }
}
