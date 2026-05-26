<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Billing;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    /**
     * @param  array<int>  $serviceIds
     */
    public function create(array $data, array $serviceIds, bool $createBilling = false): Appointment
    {
        $serviceIds = array_values(array_unique(array_map('intval', $serviceIds)));
        $services = Service::whereIn('id', $serviceIds)->get();

        return DB::transaction(function () use ($data, $serviceIds, $services, $createBilling) {
            $appointment = Appointment::create([
                'patients_id' => $data['patients_id'],
                'doctor_id'   => $data['doctor_id'],
                'service_id'  => $serviceIds[0],
                'date'        => $data['date'],
                'time'        => $data['time'],
                'status'      => $data['status'] ?? 'pending',
                'notes'       => $data['notes'] ?? null,
            ]);

            $appointment->services()->sync($serviceIds);

            if ($createBilling) {
                Billing::create([
                    'appointment_id' => $appointment->id,
                    'patients_id'    => $data['patients_id'],
                    'amount'         => $services->sum('price'),
                    'status'         => 'unpaid',
                    'description'    => $services->pluck('name')->join(', '),
                ]);
            }

            return $appointment->load('services');
        });
    }
}
