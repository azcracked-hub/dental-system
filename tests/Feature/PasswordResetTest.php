<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_loads(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertOk();
        $response->assertSee('Forgot Password');
        $response->assertSee('Send Reset Link');
    }

    public function test_reset_password_page_loads_with_token(): void
    {
        $response = $this->get('/reset-password/test-token?email=user@test.com');

        $response->assertOk();
        $response->assertSee('Reset Password');
        $response->assertSee('user@test.com');
    }

    public function test_forgot_password_requires_valid_email(): void
    {
        $response = $this->post('/forgot-password', ['email' => 'not-an-email']);

        $response->assertSessionHasErrors('email');
    }

    public function test_forgot_password_sends_notification_for_existing_user(): void
    {
        Notification::fake();

        $user = User::factory()->patient()->create(['email' => 'patient-reset@test.com']);

        $response = $this->post('/forgot-password', ['email' => 'patient-reset@test.com']);

        $response->assertSessionHas('success');
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_forgot_password_shows_error_for_unknown_email(): void
    {
        Notification::fake();

        $response = $this->post('/forgot-password', ['email' => 'unknown@test.com']);

        $response->assertSessionHas('error');
        Notification::assertNothingSent();
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->admin()->create([
            'email' => 'admin-reset@test.com',
            'password' => bcrypt('Password1'),
        ]);

        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'email' => 'admin-reset@test.com',
            'token' => $token,
            'password' => 'NewPassword1',
            'password_confirmation' => 'NewPassword1',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('NewPassword1', $user->fresh()->password));
    }

    public function test_user_can_login_after_password_reset(): void
    {
        $user = User::factory()->admin()->create([
            'email' => 'login-after-reset@test.com',
            'password' => bcrypt('Password1'),
        ]);

        $token = Password::createToken($user);

        $this->post('/reset-password', [
            'email' => 'login-after-reset@test.com',
            'token' => $token,
            'password' => 'NewPassword1',
            'password_confirmation' => 'NewPassword1',
        ]);

        $response = $this->post('/login', [
            'email' => 'login-after-reset@test.com',
            'password' => 'NewPassword1',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_reset_password_rejects_weak_password(): void
    {
        $user = User::factory()->admin()->create(['email' => 'weak@test.com']);
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'email' => 'weak@test.com',
            'token' => $token,
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_reset_password_rejects_invalid_token(): void
    {
        User::factory()->admin()->create(['email' => 'bad-token@test.com']);

        $response = $this->post('/reset-password', [
            'email' => 'bad-token@test.com',
            'token' => 'invalid-token',
            'password' => 'NewPassword1',
            'password_confirmation' => 'NewPassword1',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_authenticated_user_is_redirected_from_forgot_password_page(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/forgot-password');

        $response->assertRedirect('/dashboard');
    }
}
