<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Admin\PatientShow;
use Tests\TestCase;

class PatientUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_email_update_rejects_duplicate_user_email(): void
    {
        $admin = User::factory()->admin()->create();
        $existingUser = User::factory()->patient()->create(['email' => 'taken@example.com']);
        Patient::factory()->forUser($existingUser)->create();

        $user = User::factory()->patient()->create(['email' => 'patient@example.com']);
        $patient = Patient::factory()->forUser($user)->create(['email' => 'patient@example.com']);

        Livewire::actingAs($admin)
            ->test(PatientShow::class, ['patient' => $patient])
            ->set('email', 'taken@example.com')
            ->call('updatePatient')
            ->assertHasErrors(['email']);
    }

    public function test_controller_patient_update_rejects_duplicate_user_email(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->patient()->create(['email' => 'taken@example.com']);

        $user = User::factory()->patient()->create(['email' => 'patient@example.com']);
        $patient = Patient::factory()->forUser($user)->create(['email' => 'patient@example.com']);

        $response = $this->actingAs($admin)->patch("/admin/patients/{$patient->id}", [
            'name' => $patient->name,
            'email' => 'taken@example.com',
            'phone' => $patient->phone,
            'address' => $patient->address,
        ]);

        $response->assertSessionHasErrors('email');
    }
}
