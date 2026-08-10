<?php

namespace Tests\Feature\Auth;

use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_client_can_register_and_is_redirected_to_welcome_step(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'client',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('client-welcome', absolute: false));

        $user = User::where('email', 'client@example.com')->firstOrFail();
        $this->assertSame('client', $user->role);
        $this->assertNull($user->creatorProfile);

        Mail::assertSent(WelcomeEmail::class, fn ($mail) => $mail->hasTo($user->email));

        // The welcome step is optional — dashboard is accessible even without completing it.
        $dashboard = $this->get('/dashboard');
        $dashboard->assertStatus(200);
    }

    public function test_creator_can_register_and_is_redirected_to_onboarding(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'name' => 'Test Creator',
            'email' => 'creator@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'creator',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('onboarding', absolute: false));

        $user = User::where('email', 'creator@example.com')->firstOrFail();
        $this->assertSame('creator', $user->role);
        $this->assertNotNull($user->creatorProfile);
        $this->assertNull($user->creatorProfile->onboarding_completed_at);

        Mail::assertSent(WelcomeEmail::class, fn ($mail) => $mail->hasTo($user->email));

        // Creator without completed onboarding is bounced from dashboard back to onboarding.
        $dashboard = $this->get('/dashboard');
        $dashboard->assertRedirect(route('onboarding', absolute: false));

        $onboarding = $this->get('/onboarding');
        $onboarding->assertStatus(200);

        // Once onboarding is marked complete, dashboard access is allowed.
        $user->creatorProfile->update(['onboarding_completed_at' => now()]);
        $this->actingAs($user->fresh('creatorProfile'));

        $dashboard = $this->get('/dashboard');
        $dashboard->assertStatus(200);
    }

    public function test_registration_requires_a_valid_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'invalidrole@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertGuest();
    }
}
