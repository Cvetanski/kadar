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

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CategorySeeder::class);
    }

    private function completedContract(): Contract
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
        $contract = Contract::where('project_id', $project->id)->firstOrFail();

        $this->actingAs($client)->post("/contracts/{$contract->id}/complete");

        return $contract->fresh();
    }

    public function test_client_can_review_the_creator(): void
    {
        $contract = $this->completedContract();
        $client = $contract->client;
        $creator = $contract->creatorProfile->user;

        $response = $this->actingAs($client)->post("/contracts/{$contract->id}/reviews", [
            'rating' => 5,
            'comment' => 'Одлична соработка!',
        ]);

        $response->assertRedirect(route('projects.show', $contract->project));

        $this->assertDatabaseHas('reviews', [
            'contract_id' => $contract->id,
            'reviewer_id' => $client->id,
            'reviewee_id' => $creator->id,
            'rating' => 5,
            'comment' => 'Одлична соработка!',
        ]);
    }

    public function test_creator_can_review_the_client(): void
    {
        $contract = $this->completedContract();
        $client = $contract->client;
        $creator = $contract->creatorProfile->user;

        $response = $this->actingAs($creator)->post("/contracts/{$contract->id}/reviews", [
            'rating' => 4,
            'comment' => 'Јасна комуникација.',
        ]);

        $response->assertRedirect(route('projects.show', $contract->project));

        $this->assertDatabaseHas('reviews', [
            'contract_id' => $contract->id,
            'reviewer_id' => $creator->id,
            'reviewee_id' => $client->id,
            'rating' => 4,
        ]);
    }

    public function test_cannot_review_twice_for_the_same_contract(): void
    {
        $contract = $this->completedContract();
        $client = $contract->client;

        $this->actingAs($client)->post("/contracts/{$contract->id}/reviews", ['rating' => 5]);
        $response = $this->actingAs($client)->post("/contracts/{$contract->id}/reviews", ['rating' => 3]);

        $response->assertSessionHasErrors('review');
        $this->assertSame(1, \App\Models\Review::where('contract_id', $contract->id)->where('reviewer_id', $client->id)->count());
    }

    public function test_cannot_review_before_contract_is_completed(): void
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
        $contract = Contract::where('project_id', $project->id)->firstOrFail();

        $response = $this->actingAs($client)->post("/contracts/{$contract->id}/reviews", ['rating' => 5]);

        $response->assertForbidden();
    }

    public function test_unrelated_user_cannot_review_a_contract(): void
    {
        $contract = $this->completedContract();
        $outsider = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($outsider)->post("/contracts/{$contract->id}/reviews", ['rating' => 5]);

        $response->assertForbidden();
    }

    public function test_review_appears_on_creator_public_profile_with_average_rating(): void
    {
        $contract = $this->completedContract();
        $client = $contract->client;
        $creatorProfile = $contract->creatorProfile;

        $this->actingAs($client)->post("/contracts/{$contract->id}/reviews", [
            'rating' => 5,
            'comment' => 'Одлична соработка!',
        ]);

        $response = $this->actingAs($client)->get("/creators/{$creatorProfile->id}");

        $response->assertSee('Одлична соработка!');
        $response->assertSee($client->name);
    }

    public function test_review_appears_on_client_public_profile_with_average_rating(): void
    {
        $contract = $this->completedContract();
        $client = $contract->client;
        $creator = $contract->creatorProfile->user;

        $this->actingAs($creator)->post("/contracts/{$contract->id}/reviews", [
            'rating' => 4,
            'comment' => 'Јасна комуникација.',
        ]);

        $response = $this->actingAs($creator)->get("/clients/{$client->id}");

        $response->assertOk();
        $response->assertSee('Јасна комуникација.');
        $response->assertSee($creator->name);
    }
}
