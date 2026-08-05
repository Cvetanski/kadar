<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contract;
use App\Models\CreatorProfile;
use App\Models\Project;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CategorySeeder::class);
    }

    private function verifiedCreator(string $name): CreatorProfile
    {
        $user = User::factory()->create(['role' => 'creator', 'name' => $name]);

        return CreatorProfile::create([
            'user_id' => $user->id,
            'headline' => $name.' headline',
            'hourly_rate' => 25,
            'verified' => true,
            'onboarding_completed_at' => now(),
        ]);
    }

    public function test_guest_can_view_the_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('KADAR');
        $response->assertSee('вистински кадар', false);
    }

    public function test_guest_sees_register_and_login_buttons(): void
    {
        $response = $this->get('/');

        $response->assertSee(route('register'), false);
        $response->assertSee(route('login'), false);
        $response->assertDontSee(route('dashboard'), false);
    }

    public function test_authenticated_user_sees_dashboard_button_instead_of_register(): void
    {
        $user = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($user)->get('/');

        $response->assertSee(route('dashboard'), false);
    }

    public function test_landing_page_shows_all_five_categories_including_design(): void
    {
        $response = $this->get('/');

        $response->assertSee('Видео продукција');
        $response->assertSee('Фотографија');
        $response->assertSee('Дигитален маркетинг');
        $response->assertSee('Видео едитинг');
        $response->assertSee('Дизајн');

        $category = Category::where('slug', 'video-production')->first();
        $response->assertSee(route('creators.index', ['category_ids' => [$category->id]]), false);
    }

    public function test_landing_page_shows_only_verified_creators(): void
    {
        $verified = $this->verifiedCreator('Verified Person');

        $unverifiedUser = User::factory()->create(['role' => 'creator', 'name' => 'Unverified Person']);
        CreatorProfile::create([
            'user_id' => $unverifiedUser->id,
            'headline' => 'Unverified headline',
            'verified' => false,
            'onboarding_completed_at' => now(),
        ]);

        $response = $this->get('/');

        $response->assertSee('Verified Person');
        $response->assertDontSee('Unverified Person');
        $response->assertSee(route('creators.show', $verified), false);
    }

    public function test_landing_page_shows_new_badge_for_creator_without_reviews(): void
    {
        $this->verifiedCreator('No Reviews Person');

        $response = $this->get('/');

        $response->assertSee('Нов');
    }

    public function test_landing_page_shows_star_rating_for_creator_with_reviews(): void
    {
        $creator = $this->verifiedCreator('Reviewed Person');
        $client = User::factory()->create(['role' => 'client']);
        $category = Category::first();

        $project = Project::create([
            'client_id' => $client->id,
            'title' => 'Test project',
            'description' => 'Description',
            'status' => 'completed',
        ]);
        $project->categories()->attach($category);

        $contract = Contract::create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'creator_profile_id' => $creator->id,
            'agreed_price' => 100,
            'status' => 'completed',
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        Review::create([
            'contract_id' => $contract->id,
            'reviewer_id' => $client->id,
            'reviewee_id' => $creator->user_id,
            'rating' => 5,
            'comment' => 'Great!',
        ]);

        $response = $this->get('/');

        $response->assertSee('★★★★★');
        $response->assertSee('(1)');
    }

    public function test_landing_page_shows_empty_state_when_no_verified_creators(): void
    {
        $response = $this->get('/');

        $response->assertSee('Наскоро');
    }

    public function test_stat_band_section_is_not_rendered(): void
    {
        $response = $this->get('/');

        $response->assertDontSee('АКТИВНИ КРЕАТИВЦИ');
        $response->assertDontSee('ВРЕМЕ НА ОДГОВОР');
    }
}
