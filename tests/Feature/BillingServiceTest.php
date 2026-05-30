<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_billing_uses_total_price_for_multi_service_appointment(): void
    {
        $patient = Patient::factory()->create();
        $doctor = User::factory()->admin()->create();
        $services = collect([
            Service::factory()->create(['price' => 1000]),
            Service::factory()->create(['price' => 500]),
            Service::factory()->create(['price' => 250]),
        ]);

        $appointment = \App\Models\Appointment::factory()->create([
            'patients_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'service_id' => $services[0]->id,
            'status' => 'confirmed',
        ]);
        $appointment->services()->sync($services->pluck('id'));

        $result = app(BillingService::class)->createFromAppointment($appointment);

        $this->assertFalse($result['existed']);
        $this->assertSame(1750.0, (float) $result['billing']->amount);
        $this->assertStringContainsString(',', $result['billing']->description);
    }

    public function test_duplicate_billing_is_detected(): void
    {
        $patient = Patient::factory()->create();
        $doctor = User::factory()->admin()->create();
        $service = Service::factory()->create(['price' => 1000]);

        $appointment = \App\Models\Appointment::factory()->create([
            'patients_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'service_id' => $service->id,
            'status' => 'confirmed',
        ]);
        $appointment->services()->attach($service->id);

        $billingService = app(BillingService::class);
        $billingService->createFromAppointment($appointment);
        $result = $billingService->createFromAppointment($appointment);

        $this->assertTrue($result['existed']);
    }
}
