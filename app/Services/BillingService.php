<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Billing;

class BillingService
{
    /**
     * @return array{billing: Billing|null, existed: bool}
     */
    public function createFromAppointment(Appointment $appointment, ?float $amount = null): array
    {
        $appointment->loadMissing(['services', 'service']);

        $existing = Billing::where('appointment_id', $appointment->id)->first();
        if ($existing) {
            return ['billing' => $existing, 'existed' => true];
        }

        if ($appointment->status === 'canceled') {
            throw new \InvalidArgumentException('Cannot create billing for canceled appointments.');
        }

        $billing = Billing::create([
            'appointment_id' => $appointment->id,
            'patients_id'    => $appointment->patients_id,
            'amount'         => $amount ?? $appointment->totalServicePrice(),
            'status'         => 'unpaid',
            'description'    => $appointment->serviceNames(),
        ]);

        return ['billing' => $billing, 'existed' => false];
    }
}
