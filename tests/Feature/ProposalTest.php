<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contract;
use App\Models\CreatorProfile;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([CategorySeeder::class, CountrySeeder::class, CitySeeder::class]);
    }

    private function onboardedCreator(): User
    {
        $user = User::factory()->create(['role' => 'creator']);
        $profile = CreatorProfile::create([
            'user_id' => $user->id,
            'headline' => 'Видео продукција',
            'bio' => 'Искусен видео продуцент.',
            'onboarding_completed_at' => now(),
        ]);
        $profile->categories()->attach(Category::first());

        return $user;
    }

    private function openProject(?User $client = null): Project
    {
        $client ??= User::factory()->create(['role' => 'client']);

        $project = Project::create([
            'client_id' => $client->id,
            'title' => 'Промотивно видео',
            'description' => 'Опис на проектот.',
            'status' => 'open',
        ]);
        $project->categories()->attach(Category::first());

        return $project;
    }

    public function test_onboarded_creator_can_submit_a_proposal(): void
    {
        $creator = $this->onboardedCreator();
        $project = $this->openProject();

        $response = $this->actingAs($creator)->post("/projects/{$project->id}/proposals", [
            'message' => 'Би сакал да работам на овој проект.',
            'price' => 300,
        ]);

        $response->assertRedirect(route('projects.show', $project));
        $this->assertDatabaseHas('proposals', [
            'project_id' => $project->id,
            'creator_profile_id' => $creator->creatorProfile->id,
            'status' => 'pending',
        ]);
    }

    public function test_client_cannot_submit_a_proposal(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $project = $this->openProject();

        $response = $this->actingAs($client)->post("/projects/{$project->id}/proposals", [
            'message' => 'Test',
            'price' => 100,
        ]);

        $response->assertForbidden();
    }

    public function test_creator_without_completed_onboarding_cannot_submit_a_proposal(): void
    {
        $user = User::factory()->create(['role' => 'creator']);
        CreatorProfile::create(['user_id' => $user->id]);
        $project = $this->openProject();

        $response = $this->actingAs($user)->post("/projects/{$project->id}/proposals", [
            'message' => 'Test',
            'price' => 100,
        ]);

        $response->assertForbidden();
    }

    public function test_creator_cannot_submit_duplicate_proposal_for_same_project(): void
    {
        $creator = $this->onboardedCreator();
        $project = $this->openProject();

        $this->actingAs($creator)->post("/projects/{$project->id}/proposals", [
            'message' => 'First try',
            'price' => 300,
        ]);

        $response = $this->actingAs($creator)->post("/projects/{$project->id}/proposals", [
            'message' => 'Second try',
            'price' => 350,
        ]);

        $response->assertSessionHasErrors('project');
        $this->assertSame(1, Proposal::where('project_id', $project->id)->count());
    }

    public function test_cannot_submit_proposal_to_a_project_that_is_not_open(): void
    {
        $creator = $this->onboardedCreator();
        $project = $this->openProject();
        $project->update(['status' => 'in_progress']);

        $response = $this->actingAs($creator)->post("/projects/{$project->id}/proposals", [
            'message' => 'Test',
            'price' => 100,
        ]);

        $response->assertSessionHasErrors('project');
    }

    public function test_client_can_accept_a_proposal_creating_a_contract_and_rejecting_others(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $project = $this->openProject($client);

        $creatorA = $this->onboardedCreator();
        $creatorB = $this->onboardedCreator();

        $proposalA = Proposal::create([
            'project_id' => $project->id,
            'creator_profile_id' => $creatorA->creatorProfile->id,
            'message' => 'A',
            'price' => 300,
            'status' => 'pending',
        ]);

        $proposalB = Proposal::create([
            'project_id' => $project->id,
            'creator_profile_id' => $creatorB->creatorProfile->id,
            'message' => 'B',
            'price' => 250,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($client)->post("/proposals/{$proposalA->id}/accept");

        $response->assertRedirect(route('projects.show', $project));

        $this->assertSame('accepted', $proposalA->fresh()->status);
        $this->assertSame('rejected', $proposalB->fresh()->status);
        $this->assertSame('in_progress', $project->fresh()->status);

        $this->assertDatabaseHas('contracts', [
            'project_id' => $project->id,
            'proposal_id' => $proposalA->id,
            'client_id' => $client->id,
            'creator_profile_id' => $creatorA->creatorProfile->id,
            'agreed_price' => '300.00',
            'status' => 'active',
        ]);
    }

    public function test_only_project_owner_can_accept_a_proposal(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $otherClient = User::factory()->create(['role' => 'client']);
        $project = $this->openProject($client);
        $creator = $this->onboardedCreator();

        $proposal = Proposal::create([
            'project_id' => $project->id,
            'creator_profile_id' => $creator->creatorProfile->id,
            'message' => 'A',
            'price' => 300,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($otherClient)->post("/proposals/{$proposal->id}/accept");

        $response->assertForbidden();
        $this->assertSame('pending', $proposal->fresh()->status);
    }

    public function test_accepting_a_second_proposal_after_project_already_assigned_fails_gracefully(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $project = $this->openProject($client);

        $creatorA = $this->onboardedCreator();
        $creatorB = $this->onboardedCreator();

        $proposalA = Proposal::create([
            'project_id' => $project->id,
            'creator_profile_id' => $creatorA->creatorProfile->id,
            'message' => 'A',
            'price' => 300,
            'status' => 'pending',
        ]);

        $proposalB = Proposal::create([
            'project_id' => $project->id,
            'creator_profile_id' => $creatorB->creatorProfile->id,
            'message' => 'B',
            'price' => 250,
            'status' => 'pending',
        ]);

        // First accept succeeds and moves the project to in_progress.
        $this->actingAs($client)->post("/proposals/{$proposalA->id}/accept");

        // A second accept attempt (e.g. a race from a double-click) must not create a second contract.
        $response = $this->actingAs($client)->post("/proposals/{$proposalB->id}/accept");

        $response->assertSessionHasErrors('proposal');
        $this->assertSame(1, Contract::where('project_id', $project->id)->count());
        $this->assertSame('rejected', $proposalB->fresh()->status);
    }

    public function test_creator_sees_only_their_own_proposals_in_my_applications(): void
    {
        $creatorA = $this->onboardedCreator();
        $creatorB = $this->onboardedCreator();

        $projectA = $this->openProject();
        $projectA->update(['title' => 'Project A']);
        $projectB = $this->openProject();
        $projectB->update(['title' => 'Project B']);

        $this->actingAs($creatorA)->post("/projects/{$projectA->id}/proposals", [
            'message' => 'msg A',
            'price' => 100,
        ]);
        $this->actingAs($creatorB)->post("/projects/{$projectB->id}/proposals", [
            'message' => 'msg B',
            'price' => 150,
        ]);

        $response = $this->actingAs($creatorA)->get('/proposals');

        $response->assertSee('Project A');
        $response->assertDontSee('Project B');
    }

    public function test_client_cannot_access_my_applications_page(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($client)->get('/proposals')->assertForbidden();
    }
}
