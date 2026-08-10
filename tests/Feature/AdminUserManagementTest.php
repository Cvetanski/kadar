<?php

namespace Tests\Feature;

use App\Mail\OnboardingReminder;
use App\Models\CreatorProfile;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_delete_users(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $target = User::factory()->create(['role' => 'client']);

        $this->actingAs($client)->delete("/admin/users/{$target->id}")->assertForbidden();

        $this->assertModelExists($target);
    }

    public function test_admin_can_delete_a_creator_and_related_records_are_cascaded(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $creator = User::factory()->create(['role' => 'creator']);
        $profile = CreatorProfile::create([
            'user_id' => $creator->id,
            'onboarding_completed_at' => now(),
            'verified' => true,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/users/{$creator->id}");

        $response->assertRedirect(route('admin.users', ['role' => 'creator']));
        $response->assertSessionHas('status');

        $this->assertModelMissing($creator);
        $this->assertModelMissing($profile);
    }

    public function test_admin_can_delete_a_client_and_related_projects_are_cascaded(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $client = User::factory()->create(['role' => 'client']);
        $project = Project::create([
            'client_id' => $client->id,
            'title' => 'Test project',
            'description' => 'Test description',
            'status' => 'open',
        ]);

        $response = $this->actingAs($admin)->delete("/admin/users/{$client->id}");

        $response->assertRedirect(route('admin.users', ['role' => 'client']));

        $this->assertModelMissing($client);
        $this->assertModelMissing($project);
    }

    public function test_admin_cannot_delete_another_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $otherAdmin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);

        $this->actingAs($admin)->delete("/admin/users/{$otherAdmin->id}")->assertForbidden();

        $this->assertModelExists($otherAdmin);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);

        $response = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertModelExists($admin);
    }

    public function test_admin_can_convert_a_client_to_a_creator(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($admin)->post("/admin/users/{$client->id}/convert-to-creator");

        $response->assertRedirect(route('admin.users', ['role' => 'creator']));
        $response->assertSessionHas('status');

        $client->refresh();
        $this->assertSame('creator', $client->role);
        $this->assertNotNull($client->creatorProfile);
        $this->assertNull($client->creatorProfile->onboarding_completed_at);
    }

    public function test_converting_a_client_to_creator_keeps_their_existing_projects(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $client = User::factory()->create(['role' => 'client']);
        $project = Project::create([
            'client_id' => $client->id,
            'title' => 'Test project',
            'description' => 'Test description',
            'status' => 'open',
        ]);

        $this->actingAs($admin)->post("/admin/users/{$client->id}/convert-to-creator");

        $this->assertModelExists($project);
        $this->assertSame($client->id, $project->fresh()->client_id);
    }

    public function test_non_admin_cannot_convert_a_client_to_a_creator(): void
    {
        $someone = User::factory()->create(['role' => 'client']);
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($someone)->post("/admin/users/{$client->id}/convert-to-creator")->assertForbidden();

        $this->assertSame('client', $client->fresh()->role);
    }

    public function test_admin_cannot_convert_a_creator_to_a_creator(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $creator = User::factory()->create(['role' => 'creator']);

        $this->actingAs($admin)->post("/admin/users/{$creator->id}/convert-to-creator")->assertNotFound();
    }

    public function test_non_admin_cannot_send_onboarding_reminders(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($client)->post('/admin/creators/remind-onboarding')->assertForbidden();
    }

    public function test_admin_reminding_onboarding_emails_only_creators_with_incomplete_onboarding(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);

        $incomplete = User::factory()->create(['role' => 'creator']);
        CreatorProfile::create(['user_id' => $incomplete->id]);

        $complete = User::factory()->create(['role' => 'creator']);
        CreatorProfile::create(['user_id' => $complete->id, 'onboarding_completed_at' => now()]);

        $response = $this->actingAs($admin)->post('/admin/creators/remind-onboarding');

        $response->assertRedirect();
        $response->assertSessionHas('status');

        Mail::assertSent(OnboardingReminder::class, fn ($mail) => $mail->hasTo($incomplete->email));
        Mail::assertNotSent(OnboardingReminder::class, fn ($mail) => $mail->hasTo($complete->email));
    }

    public function test_onboarding_reminder_is_not_sent_to_creators_who_disabled_email_notifications(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);

        $incomplete = User::factory()->create(['role' => 'creator', 'email_notifications_enabled' => false]);
        CreatorProfile::create(['user_id' => $incomplete->id]);

        $this->actingAs($admin)->post('/admin/creators/remind-onboarding');

        Mail::assertNothingSent();
    }
}
