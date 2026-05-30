<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\ClinicHoliday;
use App\Models\DoctorUnavailability;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Services\SchedulingService;
use App\Support\ClinicTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SchedulingTest extends TestCase
{
    use RefreshDatabase;

    private function createPatientUser(): User
    {
        $user = User::factory()->patient()->create();
        Patient::factory()->forUser($user)->create();

        return $user;
    }

    public function test_sunday_cannot_be_booked(): void
    {
        $user = $this->createPatientUser();
        $doctor = User::factory()->admin()->create();
        $service = Service::factory()->create();
        $sunday = $this->nextSunday();

        $response = $this->actingAs($user)->post('/patient/appointments', [
            'service_ids' => [$service->id],
            'doctor_id' => $doctor->id,
            'date' => $sunday,
            'time' => '09:00',
        ]);

        $response->assertSessionHasErrors('date');
    }

    public function test_holiday_cannot_be_booked(): void
    {
        $user = $this->createPatientUser();
        $doctor = User::factory()->admin()->create();
        $service = Service::factory()->create();
        $date = $this->nextBookableDate();

        ClinicHoliday::create(['date' => $date, 'name' => 'Independence Day']);

        $response = $this->actingAs($user)->post('/patient/appointments', [
            'service_ids' => [$service->id],
            'doctor_id' => $doctor->id,
            'date' => $date,
            'time' => '09:00',
        ]);

        $response->assertSessionHasErrors('date');
    }

    public function test_doctor_unavailability_blocks_booking(): void
    {
        $user = $this->createPatientUser();
        $doctor = User::factory()->admin()->create();
        $service = Service::factory()->create();
        $date = $this->nextBookableDate();

        DoctorUnavailability::create([
            'doctor_id' => $doctor->id,
            'date' => $date,
            'reason' => 'Conference',
        ]);

        $response = $this->actingAs($user)->post('/patient/appointments', [
            'service_ids' => [$service->id],
            'doctor_id' => $doctor->id,
            'date' => $date,
            'time' => '09:00',
        ]);

        $response->assertSessionHasErrors('date');
    }

    public function test_fully_booked_day_cannot_be_booked(): void
    {
        $user = $this->createPatientUser();
        $doctor = User::factory()->admin()->create();
        $service = Service::factory()->create();
        $date = $this->nextBookableDate();
        $otherPatient = Patient::factory()->create();

        foreach (ClinicTime::SLOTS as $slot) {
            Appointment::factory()->create([
                'patients_id' => $otherPatient->id,
                'doctor_id' => $doctor->id,
                'service_id' => $service->id,
                'date' => $date,
                'time' => $slot.':00',
                'status' => 'confirmed',
            ]);
        }

        $scheduling = app(SchedulingService::class);
        $this->assertTrue($scheduling->isFullyBooked($doctor->id, $date));

        $response = $this->actingAs($user)->post('/patient/appointments', [
            'service_ids' => [$service->id],
            'doctor_id' => $doctor->id,
            'date' => $date,
            'time' => '09:00',
        ]);

        $response->assertSessionHasErrors('date');
    }

    public function test_invalid_time_outside_clinic_hours_is_rejected(): void
    {
        $user = $this->createPatientUser();
        $doctor = User::factory()->admin()->create();
        $service = Service::factory()->create();
        $date = $this->nextBookableDate();

        $response = $this->actingAs($user)->post('/patient/appointments', [
            'service_ids' => [$service->id],
            'doctor_id' => $doctor->id,
            'date' => $date,
            'time' => '17:00',
        ]);

        $response->assertSessionHasErrors('time');
    }

    public function test_doctor_unavailability_marks_appointments_for_reschedule(): void
    {
        $doctor = User::factory()->admin()->create();
        $patientUser = $this->createPatientUser();
        $service = Service::factory()->create();
        $date = $this->nextBookableDate();

        $appointment = Appointment::factory()->create([
            'patients_id' => $patientUser->patient->id,
            'doctor_id' => $doctor->id,
            'service_id' => $service->id,
            'date' => $date,
            'time' => '10:00:00',
            'status' => 'confirmed',
        ]);
        $appointment->services()->attach($service->id);

        DoctorUnavailability::create([
            'doctor_id' => $doctor->id,
            'date' => $date,
            'reason' => 'Leave',
        ]);

        $notifications = app(\App\Services\NotificationService::class);
        $count = $notifications->markAffectedAppointmentsForReschedule($doctor->id, $date, 'Leave');

        $this->assertSame(1, $count);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'needs_reschedule',
        ]);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $patientUser->id,
        ]);
    }

    public function test_availability_api_returns_month_status(): void
    {
        $user = $this->createPatientUser();
        $doctor = User::factory()->admin()->create();
        $date = $this->nextBookableDate();

        ClinicHoliday::create(['date' => $date, 'name' => 'Test Holiday']);

        $response = $this->actingAs($user)->getJson('/patient/appointments/availability/month?'.http_build_query([
            'doctor_id' => $doctor->id,
            'year' => (int) date('Y', strtotime($date)),
            'month' => (int) date('n', strtotime($date)),
        ]));

        $response->assertOk();
        $response->assertJsonPath("days.{$date}", 'holiday');
    }

    public function test_availability_slots_api_returns_12_hour_labels(): void
    {
        $user = $this->createPatientUser();
        $doctor = User::factory()->admin()->create();
        $date = $this->nextBookableDate();

        $response = $this->actingAs($user)->getJson('/patient/appointments/availability/slots?'.http_build_query([
            'doctor_id' => $doctor->id,
            'date' => $date,
        ]));

        $response->assertOk();
        $response->assertJsonStructure(['slots']);
        $slots = $response->json('slots');
        $this->assertArrayHasKey('08:00', $slots);
        $this->assertSame('8:00 AM', $slots['08:00']);
    }

    public function test_forgot_password_sends_reset_notification(): void
    {
        Notification::fake();

        $user = User::factory()->admin()->create(['email' => 'reset@test.com']);

        $response = $this->post('/forgot-password', ['email' => 'reset@test.com']);

        $response->assertSessionHas('success');
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }
}
