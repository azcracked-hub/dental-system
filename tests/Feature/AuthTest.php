<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_reach_dashboard(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('Password1'),
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'Password1',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_patient_registration_creates_linked_patient_record(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'phone' => '09171234567',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'role' => 'patient',
        ]);

        $user = User::where('email', 'jane@example.com')->first();

        $this->assertDatabaseHas('patients', [
            'email' => 'jane@example.com',
            'user_id' => $user->id,
        ]);
    }
}
