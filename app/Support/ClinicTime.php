<?php

namespace App\Support;

use Carbon\Carbon;

class ClinicTime
{
    /** Hourly slots from 8:00 AM through 4:00 PM */
    public const SLOTS = [
        '08:00', '09:00', '10:00', '11:00', '12:00',
        '13:00', '14:00', '15:00', '16:00',
    ];

    public static function to12Hour(?string $time): string
    {
        if (! $time) {
            return '—';
        }

        $normalized = strlen($time) === 5 ? $time.':00' : $time;

        return Carbon::createFromFormat('H:i:s', $normalized)->format('g:i A');
    }

    public static function normalize(?string $time): ?string
    {
        if (! $time) {
            return null;
        }

        return strlen($time) === 5 ? $time.':00' : $time;
    }

    public static function slotLabel(string $slot): string
    {
        return self::to12Hour($slot);
    }
}
