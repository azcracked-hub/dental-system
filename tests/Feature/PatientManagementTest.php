<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_patient(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/patients', [
            'name' => 'Jane Patient',
            'email' => 'jane@example.com',
            'phone' => '09171234567',
            'address' => 'Manila',
            'password' => 'Password1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('patients', ['email' => 'jane@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'jane@example.com', 'role' => 'patient']);
    }

    public function test_admin_deleting_patient_also_deletes_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->patient()->create(['email' => 'remove@example.com']);
        $patient = Patient::factory()->forUser($user)->create();

        $response = $this->actingAs($admin)->delete("/admin/patients/{$patient->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('patients', ['id' => $patient->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_patient_validation_shows_errors_for_weak_password(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/patients', [
            'name' => 'Jane Patient',
            'email' => 'jane@example.com',
            'password' => '123456',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('patients', ['email' => 'jane@example.com']);
    }
}
