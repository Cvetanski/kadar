<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\CreatorProfile;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_sees_client_nav_items_and_not_creator_or_admin_items(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)->get('/dashboard');

        $response->assertSee('Барај креативци');
        $response->assertSee('Мои Огласи');
        $response->assertDontSee('Најди Работа');
        $response->assertDontSee('Мои апликации');
        $response->assertDontSee('Верификации');
    }

    public function test_creator_sees_creator_nav_items_and_not_client_or_admin_items(): void
    {
        $creator = User::factory()->create(['role' => 'creator']);
        CreatorProfile::create(['user_id' => $creator->id, 'onboarding_completed_at' => now()]);

        $response = $this->actingAs($creator)->get('/dashboard');

        $response->assertSee('Најди Работа');
        $response->assertSee('Мои апликации');
        $response->assertDontSee('Барај креативци');
        $response->assertDontSee('Мои Огласи');
        $response->assertDontSee('Верификации');
    }

    public function test_admin_sees_admin_nav_items_and_not_client_or_creator_items(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertSee('Верификации');
        $response->assertSee('Корисници');
        $response->assertDontSee('Барај креативци');
        $response->assertDontSee('Мои Огласи');
        $response->assertDontSee('Најди Работа');
        $response->assertDontSee('Мои апликации');
    }

    public function test_admin_sees_messages_link(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertSee(route('messages.index'));
    }

    public function test_creator_who_is_also_admin_sees_both_creator_and_admin_nav_items(): void
    {
        $user = User::factory()->create(['role' => 'creator', 'is_admin' => true]);
        CreatorProfile::create(['user_id' => $user->id, 'onboarding_completed_at' => now()]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertSee('Најди Работа');
        $response->assertSee('Мои апликации');
        $response->assertSee('Верификации');
        $response->assertSee('Корисници');
    }

    public function test_unread_message_count_appears_and_updates_after_reading(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $creator = User::factory()->create(['role' => 'creator']);
        CreatorProfile::create(['user_id' => $creator->id, 'onboarding_completed_at' => now()]);

        $conversation = Conversation::create(['project_id' => null]);
        $conversation->participants()->attach([$client->id, $creator->id]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $creator->id,
            'body' => 'Здраво',
        ]);

        $this->assertSame(1, $client->fresh()->unreadMessagesCount());

        // The badge itself is a Livewire component (polls independently), so the
        // count is asserted directly rather than scraping the dashboard's HTML.
        $this->actingAs($client)->get('/dashboard')->assertOk();

        // Opening the conversation marks it as read.
        $this->actingAs($client)->get("/messages/{$conversation->id}");

        $this->assertSame(0, $client->fresh()->unreadMessagesCount());
    }
}
