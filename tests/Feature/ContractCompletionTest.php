<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contract;
use App\Models\CreatorProfile;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractCompletionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CategorySeeder::class);
    }

    private function activeContract(): Contract
    {
        $client = User::factory()->create(['role' => 'client']);
        $creatorUser = User::factory()->create(['role' => 'creator']);
        $creatorProfile = CreatorProfile::create([
            'user_id' => $creatorUser->id,
            'onboarding_completed_at' => now(),
        ]);

        $project = Project::create([
            'client_id' => $client->id,
            'title' => 'Test project',
            'description' => 'Description',
            'status' => 'open',
        ]);
        $project->categories()->attach(Category::first());

        $proposal = Proposal::create([
            'project_id' => $project->id,
            'creator_profile_id' => $creatorProfile->id,
            'message' => 'msg',
            'price' => 300,
            'status' => 'pending',
        ]);

        $this->actingAs($client)->post("/proposals/{$proposal->id}/accept");

        return Contract::where('project_id', $project->id)->firstOrFail();
    }

    public function test_client_can_mark_contract_completed(): void
    {
        $contract = $this->activeContract();
        $client = $contract->client;

        $response = $this->actingAs($client)->post("/contracts/{$contract->id}/complete");

        $response->assertRedirect(route('projects.show', $contract->project));

        $contract->refresh();
        $this->assertSame('completed', $contract->status);
        $this->assertNotNull($contract->completed_at);
        $this->assertSame('completed', $contract->project->fresh()->status);
    }

    public function test_creator_cannot_mark_contract_completed(): void
    {
        $contract = $this->activeContract();
        $creator = $contract->creatorProfile->user;

        $response = $this->actingAs($creator)->post("/contracts/{$contract->id}/complete");

        $response->assertForbidden();
        $this->assertSame('active', $contract->fresh()->status);
    }

    public function test_cannot_complete_an_already_completed_contract(): void
    {
        $contract = $this->activeContract();
        $client = $contract->client;

        $this->actingAs($client)->post("/contracts/{$contract->id}/complete");
        $response = $this->actingAs($client)->post("/contracts/{$contract->id}/complete");

        $response->assertNotFound();
    }
}
