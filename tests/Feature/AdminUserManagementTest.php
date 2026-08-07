<?php

namespace Tests\Feature;

use App\Models\CreatorProfile;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
