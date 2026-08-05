<?php

namespace Tests\Feature;

use App\Models\CreatorProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    private function creatorProfile(): CreatorProfile
    {
        $user = User::factory()->create(['role' => 'creator']);

        return CreatorProfile::create(['user_id' => $user->id, 'onboarding_completed_at' => now()]);
    }

    public function test_client_can_toggle_a_favorite_on_and_off(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $creatorProfile = $this->creatorProfile();

        $this->actingAs($client)->post("/creators/{$creatorProfile->id}/favorite");
        $this->assertDatabaseHas('favorites', [
            'user_id' => $client->id,
            'creator_profile_id' => $creatorProfile->id,
        ]);

        $this->actingAs($client)->post("/creators/{$creatorProfile->id}/favorite");
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $client->id,
            'creator_profile_id' => $creatorProfile->id,
        ]);
    }

    public function test_favorites_index_lists_saved_creators(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $creatorProfile = $this->creatorProfile();

        $this->actingAs($client)->post("/creators/{$creatorProfile->id}/favorite");

        $response = $this->actingAs($client)->get('/favorites');

        $response->assertSee($creatorProfile->user->name);
    }

    public function test_creator_cannot_favorite_their_own_profile(): void
    {
        $creatorProfile = $this->creatorProfile();

        $response = $this->actingAs($creatorProfile->user)->post("/creators/{$creatorProfile->id}/favorite");

        $response->assertForbidden();
    }
}
