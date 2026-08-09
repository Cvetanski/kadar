<?php

namespace Tests\Feature;

use App\Models\CreatorProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class PortfolioManagerTest extends TestCase
{
    use RefreshDatabase;

    private function profile(): CreatorProfile
    {
        $creator = User::factory()->create(['role' => 'creator']);

        return CreatorProfile::create([
            'user_id' => $creator->id,
            'headline' => 'Videographer',
            'onboarding_completed_at' => now(),
        ]);
    }

    public function test_owner_can_add_a_portfolio_item(): void
    {
        $profile = $this->profile();

        Livewire::actingAs($profile->user)->test('portfolio-manager', ['creatorProfile' => $profile])
            ->set('newTitle', 'Свадба - Марија и Стефан')
            ->set('newType', 'video')
            ->set('newUrl', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ')
            ->call('addItem')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('portfolio_items', [
            'creator_profile_id' => $profile->id,
            'title' => 'Свадба - Марија и Стефан',
            'media_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);
    }

    public function test_adding_a_vimeo_item_stores_its_fetched_thumbnail(): void
    {
        Http::fake([
            'vimeo.com/api/oembed.json*' => Http::response(['thumbnail_url' => 'https://i.vimeocdn.com/video/fake.jpg']),
        ]);

        $profile = $this->profile();

        Livewire::actingAs($profile->user)->test('portfolio-manager', ['creatorProfile' => $profile])
            ->set('newUrl', 'https://vimeo.com/1084537')
            ->call('addItem')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('portfolio_items', [
            'creator_profile_id' => $profile->id,
            'media_url' => 'https://vimeo.com/1084537',
            'thumbnail_url' => 'https://i.vimeocdn.com/video/fake.jpg',
        ]);
    }

    public function test_adding_an_item_requires_a_valid_url(): void
    {
        $profile = $this->profile();

        Livewire::actingAs($profile->user)->test('portfolio-manager', ['creatorProfile' => $profile])
            ->set('newUrl', 'not-a-url')
            ->call('addItem')
            ->assertHasErrors('newUrl');

        $this->assertDatabaseCount('portfolio_items', 0);
    }

    public function test_owner_can_remove_a_portfolio_item(): void
    {
        $profile = $this->profile();
        $item = $profile->portfolioItems()->create([
            'title' => 'Old item',
            'media_type' => 'video',
            'media_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        Livewire::actingAs($profile->user)->test('portfolio-manager', ['creatorProfile' => $profile])
            ->call('removeItem', $item->id);

        $this->assertModelMissing($item);
    }

    public function test_another_creator_cannot_manage_someone_elses_portfolio(): void
    {
        $profile = $this->profile();
        $otherCreator = User::factory()->create(['role' => 'creator']);

        Livewire::actingAs($otherCreator)->test('portfolio-manager', ['creatorProfile' => $profile])
            ->assertForbidden();
    }

    public function test_admin_can_manage_a_creators_portfolio(): void
    {
        $profile = $this->profile();
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);

        Livewire::actingAs($admin)->test('portfolio-manager', ['creatorProfile' => $profile])
            ->set('newUrl', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ')
            ->call('addItem')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('portfolio_items', ['creator_profile_id' => $profile->id]);
    }

    public function test_client_cannot_manage_a_creators_portfolio(): void
    {
        $profile = $this->profile();
        $client = User::factory()->create(['role' => 'client']);

        Livewire::actingAs($client)->test('portfolio-manager', ['creatorProfile' => $profile])
            ->assertForbidden();
    }
}
