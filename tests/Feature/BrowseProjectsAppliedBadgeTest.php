<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CreatorProfile;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BrowseProjectsAppliedBadgeTest extends TestCase
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
            'onboarding_completed_at' => now(),
        ]);
        $profile->categories()->attach(Category::first());

        return $user;
    }

    private function openProject(): Project
    {
        $client = User::factory()->create(['role' => 'client']);

        $project = Project::create([
            'client_id' => $client->id,
            'title' => 'Промотивно видео',
            'description' => 'Опис на проектот.',
            'status' => 'open',
        ]);
        $project->categories()->attach(Category::first());

        return $project;
    }

    public function test_no_badge_shown_when_creator_has_not_applied(): void
    {
        $creator = $this->onboardedCreator();
        $project = $this->openProject();

        Livewire::actingAs($creator)
            ->test('browse-projects')
            ->call('selectProject', $project->id)
            ->assertDontSee('Веќе аплициравте');
    }

    public function test_badge_shown_when_creator_has_already_applied(): void
    {
        $creator = $this->onboardedCreator();
        $project = $this->openProject();

        Proposal::create([
            'project_id' => $project->id,
            'creator_profile_id' => $creator->creatorProfile->id,
            'message' => 'Ме интересира овој проект.',
            'price' => 100,
            'status' => 'pending',
        ]);

        Livewire::actingAs($creator)
            ->test('browse-projects')
            ->call('selectProject', $project->id)
            ->assertSee('Веќе аплициравте');
    }
}
