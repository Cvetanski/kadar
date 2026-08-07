<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\CreatorProfile;
use App\Models\Skill;
use App\Models\User;
use App\Services\GeoIpService;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CountrySeeder;
use Database\Seeders\SkillSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([CategorySeeder::class, CountrySeeder::class, CitySeeder::class, SkillSeeder::class]);
    }

    private function creator(): User
    {
        $user = User::factory()->create(['role' => 'creator']);
        CreatorProfile::create(['user_id' => $user->id]);

        return $user;
    }

    public function test_client_role_is_redirected_away_from_onboarding_page(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)->get('/onboarding');

        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_already_onboarded_creator_is_redirected_to_dashboard(): void
    {
        $creator = $this->creator();
        $creator->creatorProfile->update(['onboarding_completed_at' => now()]);

        $response = $this->actingAs($creator)->get('/onboarding');

        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_creator_can_view_the_onboarding_wizard(): void
    {
        $creator = $this->creator();

        $response = $this->actingAs($creator)->get('/onboarding');

        $response->assertStatus(200);
        $response->assertSee('Која е твојата специјалност?');
    }

    public function test_next_step_requires_at_least_one_category(): void
    {
        $creator = $this->creator();

        Livewire::actingAs($creator)->test('onboarding-wizard')
            ->call('nextStep')
            ->assertHasErrors('categoryIds');
    }

    public function test_toggling_a_category_adds_and_removes_it(): void
    {
        $creator = $this->creator();
        $category = Category::first();

        Livewire::actingAs($creator)->test('onboarding-wizard')
            ->call('toggleCategory', $category->id)
            ->assertSet('categoryIds', [$category->id])
            ->call('toggleCategory', $category->id)
            ->assertSet('categoryIds', []);
    }

    public function test_selecting_a_category_unlocks_its_skills_and_advances_to_step_2(): void
    {
        $creator = $this->creator();
        $category = Category::where('slug', 'video-production')->firstOrFail();
        $skill = Skill::where('category_id', $category->id)->firstOrFail();

        Livewire::actingAs($creator)->test('onboarding-wizard')
            ->call('toggleCategory', $category->id)
            ->call('nextStep')
            ->assertSet('step', 2)
            ->call('toggleSkill', $skill->id)
            ->assertSet('skillIds', [$skill->id]);
    }

    public function test_next_step_requires_at_least_three_skills_selected(): void
    {
        $creator = $this->creator();
        $category = Category::where('slug', 'video-production')->firstOrFail();
        $skills = Skill::where('category_id', $category->id)->limit(2)->get();

        $component = Livewire::actingAs($creator)->test('onboarding-wizard')
            ->call('toggleCategory', $category->id)
            ->call('nextStep')
            ->assertSet('step', 2);

        foreach ($skills as $skill) {
            $component->call('toggleSkill', $skill->id);
        }

        $component->call('nextStep')
            ->assertHasErrors('skillIds')
            ->assertSet('step', 2);
    }

    public function test_three_skills_across_multiple_categories_satisfy_the_minimum(): void
    {
        $creator = $this->creator();
        $videoCategory = Category::where('slug', 'video-production')->firstOrFail();
        $editingCategory = Category::where('slug', 'video-editing')->firstOrFail();
        $videoSkills = Skill::where('category_id', $videoCategory->id)->limit(2)->get();
        $editingSkill = Skill::where('category_id', $editingCategory->id)->firstOrFail();

        $component = Livewire::actingAs($creator)->test('onboarding-wizard')
            ->call('toggleCategory', $videoCategory->id)
            ->call('toggleCategory', $editingCategory->id)
            ->call('nextStep')
            ->assertSet('step', 2);

        foreach ($videoSkills as $skill) {
            $component->call('toggleSkill', $skill->id);
        }

        $component->call('toggleSkill', $editingSkill->id)
            ->call('nextStep')
            ->assertHasNoErrors('skillIds')
            ->assertSet('step', 3);
    }

    public function test_location_step_requires_city_unless_remote(): void
    {
        $creator = $this->creator();
        $country = Country::first();

        Livewire::actingAs($creator)->test('onboarding-wizard')
            ->set('step', 3)
            ->set('countryId', $country->id)
            ->call('nextStep')
            ->assertHasErrors('cityId');
    }

    public function test_location_step_allows_remote_without_city(): void
    {
        $creator = $this->creator();
        $country = Country::first();

        Livewire::actingAs($creator)->test('onboarding-wizard')
            ->set('step', 3)
            ->set('countryId', $country->id)
            ->set('remoteOk', true)
            ->call('nextStep')
            ->assertHasNoErrors()
            ->assertSet('step', 4);
    }

    public function test_changing_country_resets_selected_city(): void
    {
        $creator = $this->creator();
        $countryA = Country::first();
        $cityA = City::where('country_id', $countryA->id)->firstOrFail();

        Livewire::actingAs($creator)->test('onboarding-wizard')
            ->set('countryId', $countryA->id)
            ->set('cityId', $cityA->id)
            ->set('countryId', Country::where('id', '!=', $countryA->id)->first()->id)
            ->assertSet('cityId', null);
    }

    public function test_portfolio_step_requires_at_least_one_item(): void
    {
        $creator = $this->creator();

        Livewire::actingAs($creator)->test('onboarding-wizard')
            ->set('step', 5)
            ->call('nextStep')
            ->assertHasErrors('portfolioItems');
    }

    public function test_can_add_and_remove_a_portfolio_item(): void
    {
        $creator = $this->creator();

        $component = Livewire::actingAs($creator)->test('onboarding-wizard')
            ->set('newPortfolioTitle', 'Демо рил')
            ->set('newPortfolioType', 'video')
            ->set('newPortfolioUrl', 'https://example.com/reel.mp4')
            ->call('addPortfolioItem');

        $component->assertCount('portfolioItems', 1);

        $component->call('removePortfolioItem', 0)
            ->assertCount('portfolioItems', 0);
    }

    public function test_adding_a_portfolio_item_requires_a_valid_url(): void
    {
        $creator = $this->creator();

        Livewire::actingAs($creator)->test('onboarding-wizard')
            ->set('newPortfolioUrl', 'not-a-url')
            ->call('addPortfolioItem')
            ->assertHasErrors('newPortfolioUrl');
    }

    public function test_full_wizard_submission_persists_everything_in_one_transaction(): void
    {
        $creator = $this->creator();
        $category = Category::where('slug', 'video-production')->firstOrFail();
        $skills = Skill::where('category_id', $category->id)->limit(3)->get();
        $country = Country::first();
        $city = City::where('country_id', $country->id)->firstOrFail();

        $component = Livewire::actingAs($creator)->test('onboarding-wizard')
            ->call('toggleCategory', $category->id);

        foreach ($skills as $skill) {
            $component->call('toggleSkill', $skill->id);
        }

        $component
            ->set('countryId', $country->id)
            ->set('cityId', $city->id)
            ->set('headline', 'Видео продукција & дрон снимање')
            ->set('bio', 'Професионален видео продуцент со 5 години искуство.')
            ->set('hourlyRate', 35)
            ->set('experienceYears', 5)
            ->set('newPortfolioUrl', 'https://example.com/reel.mp4')
            ->call('addPortfolioItem')
            ->set('instagramUrl', 'https://instagram.com/demo')
            ->call('submit')
            ->assertRedirect(route('dashboard', absolute: false));

        $creator->refresh();
        $profile = $creator->creatorProfile->fresh(['categories', 'skills', 'portfolioItems']);

        $this->assertNotNull($profile->onboarding_completed_at);
        $this->assertFalse($profile->verified);
        $this->assertSame('Видео продукција & дрон снимање', $profile->headline);
        $this->assertSame('35.00', $profile->hourly_rate);
        $this->assertSame($country->id, $creator->country_id);
        $this->assertSame($city->id, $creator->city_id);
        $this->assertTrue($profile->categories->contains($category));
        $this->assertCount(3, $profile->skills);
        foreach ($skills as $skill) {
            $this->assertTrue($profile->skills->contains($skill));
        }
        $this->assertCount(1, $profile->portfolioItems);
        $this->assertSame('https://example.com/reel.mp4', $profile->portfolioItems->first()->media_url);
        $this->assertSame('https://instagram.com/demo', $profile->instagram_url);

        // Onboarding is now complete, so /dashboard should no longer bounce this creator away.
        $this->get('/dashboard')->assertStatus(200);
    }

    public function test_country_is_prefilled_from_a_successful_geoip_lookup(): void
    {
        $mk = Country::where('code', 'MK')->firstOrFail();

        $this->mock(GeoIpService::class, function ($mock) {
            $mock->shouldReceive('guessCountryCode')->once()->andReturn('MK');
        });

        $creator = $this->creator();

        $component = Livewire::actingAs($creator)->test('onboarding-wizard');

        $this->assertSame($mk->id, $component->get('countryId'));
    }

    public function test_country_is_left_unselected_when_geoip_lookup_fails(): void
    {
        $this->mock(GeoIpService::class, function ($mock) {
            $mock->shouldReceive('guessCountryCode')->once()->andReturn(null);
        });

        $creator = $this->creator();

        $component = Livewire::actingAs($creator)->test('onboarding-wizard');

        $this->assertNull($component->get('countryId'));
    }

    public function test_skip_marks_the_profile_as_skipped_and_redirects_to_dashboard(): void
    {
        $creator = $this->creator();

        Livewire::actingAs($creator)->test('onboarding-wizard')
            ->call('skip')
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertNotNull($creator->creatorProfile->fresh()->onboarding_skipped_at);
        $this->assertNull($creator->creatorProfile->fresh()->onboarding_completed_at);
    }

    public function test_skipped_creator_is_no_longer_forced_into_onboarding(): void
    {
        $creator = $this->creator();
        $creator->creatorProfile->update(['onboarding_skipped_at' => now()]);

        $this->actingAs($creator)->get('/dashboard')->assertStatus(200);
        $this->actingAs($creator)->get('/browse')->assertStatus(200);
    }

    public function test_skipped_creator_can_still_open_onboarding_to_finish_later(): void
    {
        $creator = $this->creator();
        $creator->creatorProfile->update(['onboarding_skipped_at' => now()]);

        $response = $this->actingAs($creator)->get('/onboarding');

        $response->assertStatus(200);
        $response->assertSee('Која е твојата специјалност?');
    }

    public function test_not_yet_started_creator_is_still_forced_into_onboarding(): void
    {
        $creator = $this->creator();

        $this->actingAs($creator)->get('/dashboard')->assertRedirect(route('onboarding', absolute: false));
    }

    public function test_dashboard_shows_reminder_banner_for_skipped_incomplete_profile(): void
    {
        $creator = $this->creator();
        $creator->creatorProfile->update(['onboarding_skipped_at' => now()]);

        $response = $this->actingAs($creator)->get('/dashboard');

        $response->assertSee('Го прескокна поставувањето на профилот');
        $response->assertSee(route('onboarding'), false);
    }

    public function test_dashboard_does_not_show_reminder_banner_once_onboarding_is_completed(): void
    {
        $creator = $this->creator();
        $creator->creatorProfile->update(['onboarding_completed_at' => now()]);

        $response = $this->actingAs($creator)->get('/dashboard');

        $response->assertDontSee('Го прескокна поставувањето на профилот');
    }

    public function test_dashboard_empty_state_shows_finish_profile_button_when_onboarding_incomplete(): void
    {
        $creator = $this->creator();
        $creator->creatorProfile->update(['onboarding_skipped_at' => now()]);

        $response = $this->actingAs($creator)->get('/dashboard');

        $response->assertSee('CreatorSpot штотуку почна');
        $response->assertSee(route('onboarding'), false);
    }

    public function test_dashboard_empty_state_hides_finish_profile_button_once_onboarding_completed(): void
    {
        $creator = $this->creator();
        $creator->creatorProfile->update(['onboarding_completed_at' => now()]);

        $response = $this->actingAs($creator)->get('/dashboard');

        $response->assertSee('CreatorSpot штотуку почна');
        $response->assertSee('Прегледај ги сите категории');
        $response->assertDontSee('Дополни го профилот');
    }
}
