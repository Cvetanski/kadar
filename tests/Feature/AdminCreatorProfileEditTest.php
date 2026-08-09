<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Country;
use App\Models\CreatorProfile;
use App\Models\Skill;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CountrySeeder;
use Database\Seeders\SkillSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCreatorProfileEditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([CategorySeeder::class, CountrySeeder::class, CitySeeder::class, SkillSeeder::class]);
    }

    public function test_non_admin_cannot_access_the_edit_page(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $creator = User::factory()->create(['role' => 'creator']);

        $this->actingAs($client)->get("/admin/creators/{$creator->id}/edit")->assertForbidden();
    }

    public function test_admin_can_open_the_edit_page_for_a_creator_with_no_profile_yet(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $creator = User::factory()->create(['role' => 'creator']);

        $response = $this->actingAs($admin)->get("/admin/creators/{$creator->id}/edit");

        $response->assertOk();
        $this->assertDatabaseHas('creator_profiles', ['user_id' => $creator->id]);
    }

    public function test_admin_cannot_open_the_edit_page_for_a_client(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($admin)->get("/admin/creators/{$client->id}/edit")->assertForbidden();
    }

    public function test_admin_can_complete_a_creators_onboarding(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $creator = User::factory()->create(['role' => 'creator']);
        $category = Category::first();
        $skills = Skill::whereHas('categories', fn ($q) => $q->where('categories.id', $category->id))->take(3)->pluck('id');
        $country = Country::first();

        $response = $this->actingAs($admin)->patch("/admin/creators/{$creator->id}", [
            'name' => $creator->name,
            'headline' => 'Видеограф за настани',
            'category_ids' => [$category->id],
            'skill_ids' => $skills->all(),
            'country_id' => $country->id,
            'onboarding_completed' => '1',
        ]);

        $response->assertRedirect(route('admin.users', ['role' => 'creator']));
        $response->assertSessionHas('status');

        $profile = CreatorProfile::where('user_id', $creator->id)->firstOrFail();
        $this->assertNotNull($profile->onboarding_completed_at);
        $this->assertSame('Видеограф за настани', $profile->headline);
        $this->assertTrue($profile->categories->contains($category));
        $this->assertSame($country->id, $creator->fresh()->country_id);
    }

    public function test_admin_cannot_mark_a_profile_verified_without_completing_onboarding(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $creator = User::factory()->create(['role' => 'creator']);
        $category = Category::first();

        $this->actingAs($admin)->patch("/admin/creators/{$creator->id}", [
            'name' => $creator->name,
            'headline' => 'Видеограф',
            'category_ids' => [$category->id],
            'verified' => '1',
        ]);

        $profile = CreatorProfile::where('user_id', $creator->id)->firstOrFail();
        $this->assertNull($profile->onboarding_completed_at);
        $this->assertFalse($profile->verified);
    }

    public function test_admin_can_unmark_a_completed_onboarding(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $creator = User::factory()->create(['role' => 'creator']);
        $profile = CreatorProfile::create([
            'user_id' => $creator->id,
            'headline' => 'Old headline',
            'onboarding_completed_at' => now(),
            'verified' => true,
        ]);
        $category = Category::first();
        $profile->categories()->attach($category);

        $this->actingAs($admin)->patch("/admin/creators/{$creator->id}", [
            'name' => $creator->name,
            'headline' => 'Old headline',
            'category_ids' => [$category->id],
        ]);

        $profile->refresh();
        $this->assertNull($profile->onboarding_completed_at);
        $this->assertFalse($profile->verified);
    }
}
