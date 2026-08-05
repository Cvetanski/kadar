<?php

namespace Tests\Feature;

use App\Models\CreatorProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_verifications_page(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($client)->get('/admin/verifications')->assertForbidden();
    }

    public function test_admin_sees_only_onboarded_unverified_creators(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);

        $pendingUser = User::factory()->create(['role' => 'creator', 'name' => 'Pending Creator']);
        CreatorProfile::create([
            'user_id' => $pendingUser->id,
            'onboarding_completed_at' => now(),
            'verified' => false,
        ]);

        $verifiedUser = User::factory()->create(['role' => 'creator', 'name' => 'Already Verified']);
        CreatorProfile::create([
            'user_id' => $verifiedUser->id,
            'onboarding_completed_at' => now(),
            'verified' => true,
        ]);

        $incompleteUser = User::factory()->create(['role' => 'creator', 'name' => 'Incomplete Creator']);
        CreatorProfile::create(['user_id' => $incompleteUser->id, 'verified' => false]);

        $response = $this->actingAs($admin)->get('/admin/verifications');

        $response->assertSee('Pending Creator');
        $response->assertDontSee('Already Verified');
        $response->assertDontSee('Incomplete Creator');
    }

    public function test_admin_can_verify_a_creator(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $user = User::factory()->create(['role' => 'creator']);
        $profile = CreatorProfile::create([
            'user_id' => $user->id,
            'onboarding_completed_at' => now(),
            'verified' => false,
        ]);

        $response = $this->actingAs($admin)->post("/admin/verifications/{$profile->id}");

        $response->assertRedirect();
        $this->assertTrue($profile->fresh()->verified);
    }

    public function test_non_admin_cannot_verify_a_creator(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $user = User::factory()->create(['role' => 'creator']);
        $profile = CreatorProfile::create([
            'user_id' => $user->id,
            'onboarding_completed_at' => now(),
            'verified' => false,
        ]);

        $this->actingAs($client)->post("/admin/verifications/{$profile->id}")->assertForbidden();
        $this->assertFalse($profile->fresh()->verified);
    }

    public function test_non_admin_cannot_access_users_page(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($client)->get('/admin/users')->assertForbidden();
    }

    public function test_admin_sees_all_creators_and_all_clients(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);

        $creator = User::factory()->create(['role' => 'creator', 'name' => 'Some Creator']);
        CreatorProfile::create(['user_id' => $creator->id, 'onboarding_completed_at' => now(), 'verified' => false]);

        $client = User::factory()->create(['role' => 'client', 'name' => 'Some Client']);

        $creatorsResponse = $this->actingAs($admin)->get('/admin/users?role=creator');
        $creatorsResponse->assertSee('Some Creator');
        $creatorsResponse->assertDontSee('Some Client');

        $clientsResponse = $this->actingAs($admin)->get('/admin/users?role=client');
        $clientsResponse->assertSee('Some Client');
        $clientsResponse->assertDontSee('Some Creator');
    }
}
