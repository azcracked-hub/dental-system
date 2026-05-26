<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Appointment> */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'patients_id' => Patient::factory(),
            'doctor_id' => User::factory()->admin(),
            'service_id' => Service::factory(),
            'date' => now()->addDays(3)->toDateString(),
            'time' => '09:00:00',
            'status' => 'pending',
            'notes' => null,
        ];
    }
}
