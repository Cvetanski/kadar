<?php

namespace Tests\Feature;

use App\Models\CreatorProfile;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BrowseProjectsEmptyStateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([CategorySeeder::class, CountrySeeder::class, CitySeeder::class]);
    }

    public function test_onboarded_creator_sees_the_positive_empty_state_without_the_finish_profile_button(): void
    {
        $user = User::factory()->create(['role' => 'creator']);
        CreatorProfile::create([
            'user_id' => $user->id,
            'headline' => 'Видео продукција',
            'onboarding_completed_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test('browse-projects')
            ->assertSee('CreatorSpot штотуку почна')
            ->assertDontSee('Дополни го профилот')
            ->assertDontSee('Нема отворени огласи што одговараат на филтрите.');
    }

    public function test_creator_with_incomplete_onboarding_is_linked_back_to_the_wizard(): void
    {
        $user = User::factory()->create(['role' => 'creator']);
        CreatorProfile::create([
            'user_id' => $user->id,
            'onboarding_skipped_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test('browse-projects')
            ->assertSee('CreatorSpot штотуку почна')
            ->assertSee(route('onboarding'), false);
    }

    public function test_browse_all_categories_link_is_not_shown_since_the_page_already_shows_everything(): void
    {
        $user = User::factory()->create(['role' => 'creator']);
        CreatorProfile::create([
            'user_id' => $user->id,
            'onboarding_completed_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test('browse-projects')
            ->assertDontSee('Прегледај ги сите категории');
    }

    public function test_client_sees_the_generic_empty_state_instead(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        Livewire::actingAs($client)
            ->test('browse-projects')
            ->assertSee('Нема отворени огласи што одговараат на филтрите.')
            ->assertDontSee('CreatorSpot штотуку почна');
    }
}
