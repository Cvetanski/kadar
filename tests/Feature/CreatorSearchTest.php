<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CreatorProfile;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatorSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([CategorySeeder::class, CountrySeeder::class, CitySeeder::class]);
    }

    private function onboardedCreator(string $name, bool $verified = false): CreatorProfile
    {
        $user = User::factory()->create(['role' => 'creator', 'name' => $name]);
        $profile = CreatorProfile::create([
            'user_id' => $user->id,
            'headline' => $name.' headline',
            'bio' => 'Bio',
            'verified' => $verified,
            'onboarding_completed_at' => now(),
        ]);
        $profile->categories()->attach(Category::first());

        return $profile;
    }

    public function test_search_shows_onboarded_creators_regardless_of_verified_status(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $this->onboardedCreator('Unverified Creator', verified: false);
        $this->onboardedCreator('Verified Creator', verified: true);

        $response = $this->actingAs($client)->get('/creators');

        $response->assertSee('Unverified Creator');
        $response->assertSee('Verified Creator');
    }

    public function test_search_excludes_creators_without_completed_onboarding(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $incompleteUser = User::factory()->create(['role' => 'creator', 'name' => 'Incomplete Creator']);
        CreatorProfile::create(['user_id' => $incompleteUser->id]);

        $response = $this->actingAs($client)->get('/creators');

        $response->assertDontSee('Incomplete Creator');
    }

    public function test_search_can_filter_by_category(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $video = Category::where('slug', 'video-production')->first();
        $photo = Category::where('slug', 'photography')->first();

        $videoCreator = $this->onboardedCreator('Video Person');
        $videoCreator->categories()->sync([$video->id]);

        $photoCreator = $this->onboardedCreator('Photo Person');
        $photoCreator->categories()->sync([$photo->id]);

        $response = $this->actingAs($client)->get('/creators?category_ids[]='.$video->id);

        $response->assertSee('Video Person');
        $response->assertDontSee('Photo Person');
    }

    public function test_creator_profile_shows_invite_and_favorite_buttons_to_other_users(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        Project::create([
            'client_id' => $client->id,
            'title' => 'Test project',
            'description' => 'Test description',
            'status' => 'open',
        ]);
        $creatorProfile = $this->onboardedCreator('Some Creator');

        $response = $this->actingAs($client)->get("/creators/{$creatorProfile->id}");

        $response->assertSee('Покани на проект');
        $response->assertSee('Зачувај');
    }

    public function test_creator_does_not_see_message_button_on_own_profile(): void
    {
        $creatorProfile = $this->onboardedCreator('Self Creator');

        $response = $this->actingAs($creatorProfile->user)->get("/creators/{$creatorProfile->id}");

        $response->assertDontSee('Прати порака');
    }

    public function test_guest_can_view_creator_search_and_profile(): void
    {
        $creatorProfile = $this->onboardedCreator('Public Creator', verified: true);

        $this->get('/creators')
            ->assertOk()
            ->assertSee('Public Creator');

        $this->get("/creators/{$creatorProfile->id}")
            ->assertOk()
            ->assertSee('Public Creator');
    }
}
