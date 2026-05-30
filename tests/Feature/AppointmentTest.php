<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    private function createPatientUser(): User
    {
        $user = User::factory()->patient()->create();
        Patient::factory()->forUser($user)->create();

        return $user;
    }

    public function test_patient_can_book_appointment_with_one_service(): void
    {
        $user = $this->createPatientUser();
        $doctor = User::factory()->admin()->create();
        $service = Service::factory()->create();

        $response = $this->actingAs($user)->post('/patient/appointments', [
            'service_ids' => [$service->id],
            'doctor_id' => $doctor->id,
            'date' => $this->nextBookableDate(),
            'time' => '09:00',
        ]);

        $response->assertRedirect('/patient/dashboard');

        $appointment = Appointment::first();
        $this->assertNotNull($appointment);
        $this->assertCount(1, $appointment->services);
    }

    public function test_patient_can_book_with_two_services(): void
    {
        $user = $this->createPatientUser();
        $doctor = User::factory()->admin()->create();
        $services = Service::factory()->count(2)->create();

        $response = $this->actingAs($user)->post('/patient/appointments', [
            'service_ids' => $services->pluck('id')->all(),
            'doctor_id' => $doctor->id,
            'date' => $this->nextBookableDate(),
            'time' => '09:00',
        ]);

        $response->assertRedirect('/patient/dashboard');
        $this->assertCount(2, Appointment::first()->services);
    }

    public function test_patient_cannot_book_more_than_three_services(): void
    {
        $user = $this->createPatientUser();
        $doctor = User::factory()->admin()->create();
        $services = Service::factory()->count(4)->create();

        $response = $this->actingAs($user)->post('/patient/appointments', [
            'service_ids' => $services->pluck('id')->all(),
            'doctor_id' => $doctor->id,
            'date' => $this->nextBookableDate(),
            'time' => '09:00',
        ]);

        $response->assertSessionHasErrors('service_ids');
    }

    public function test_admin_can_create_appointment_with_multiple_services(): void
    {
        $admin = User::factory()->admin()->create();
        $patient = Patient::factory()->create();
        $services = Service::factory()->count(3)->create();

        $response = $this->actingAs($admin)->post('/admin/appointments', [
            'patients_id' => $patient->id,
            'doctor_id' => $admin->id,
            'service_ids' => $services->pluck('id')->all(),
            'date' => $this->nextBookableDate(),
            'time' => '10:00',
        ]);

        $response->assertRedirect();
        $this->assertCount(3, Appointment::first()->services);
    }

    public function test_patient_dashboard_loads(): void
    {
        $user = User::factory()->patient()->create();
        Patient::factory()->forUser($user)->create();

        $response = $this->actingAs($user)->get('/patient/dashboard');

        $response->assertOk();
    }

    public function test_patient_can_cancel_appointment(): void
    {
        $user = $this->createPatientUser();
        $patient = $user->patient;
        $doctor = User::factory()->admin()->create();
        $service = Service::factory()->create();

        $appointment = Appointment::factory()->create([
            'patients_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'service_id' => $service->id,
            'status' => 'pending',
        ]);
        $appointment->services()->attach($service->id);

        $response = $this->actingAs($user)->patch("/patient/appointments/{$appointment->id}/cancel");

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'canceled',
        ]);
    }

    public function test_staff_cannot_access_admin_dashboard(): void
    {
        $staff = User::factory()->staff()->create();

        $response = $this->actingAs($staff)->get('/admin/dashboard');

        $response->assertRedirect(route('staff.dashboard'));
    }

    public function test_staff_can_access_staff_dashboard(): void
    {
        $staff = User::factory()->staff()->create();

        $response = $this->actingAs($staff)->get('/staff/dashboard');

        $response->assertOk();
    }

    public function test_clinical_notes_page_lists_completed_notes(): void
    {
        $admin = User::factory()->admin()->create();
        $patient = Patient::factory()->create();
        $doctor = User::factory()->admin()->create();
        $service = Service::factory()->create();

        $appointment = Appointment::factory()->create([
            'patients_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'service_id' => $service->id,
            'status' => 'completed',
            'notes' => 'Patient responded well to treatment.',
        ]);
        $appointment->services()->attach($service->id);

        $response = $this->actingAs($admin)->get('/admin/clinical-notes');

        $response->assertOk();
        $response->assertSee('Patient responded well to treatment.');
    }

    public function test_double_booking_is_rejected(): void
    {
        $user = $this->createPatientUser();
        $doctor = User::factory()->admin()->create();
        $service = Service::factory()->create();
        $date = $this->nextBookableDate(2);

        Appointment::factory()->create([
            'patients_id' => Patient::factory()->create()->id,
            'doctor_id' => $doctor->id,
            'service_id' => $service->id,
            'date' => $date,
            'time' => '10:00:00',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->post('/patient/appointments', [
            'service_ids' => [$service->id],
            'doctor_id' => $doctor->id,
            'date' => $date,
            'time' => '10:00',
        ]);

        $response->assertSessionHasErrors('date');
    }
}
