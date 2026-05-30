<?php

namespace App\Http\Controllers;

use App\Services\SchedulingService;
use Illuminate\Http\Request;

class AppointmentAvailabilityController extends Controller
{
    public function month(Request $request, SchedulingService $scheduling)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'year' => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        return response()->json([
            'days' => $scheduling->getMonthAvailability(
                (int) $request->doctor_id,
                (int) $request->year,
                (int) $request->month
            ),
        ]);
    }

    public function slots(Request $request, SchedulingService $scheduling)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $doctorId = (int) $request->doctor_id;
        $date = $request->date;
        $reason = $scheduling->getDayBlockReason($doctorId, $date);

        return response()->json([
            'reason' => $reason,
            'slots' => $reason ? [] : $scheduling->getAvailableSlots($doctorId, $date),
        ]);
    }
}
