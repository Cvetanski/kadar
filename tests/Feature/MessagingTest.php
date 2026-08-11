<?php

namespace Tests\Feature;

use App\Mail\NewMessageNotification;
use App\Models\Conversation;
use App\Models\CreatorProfile;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class MessagingTest extends TestCase
{
    use RefreshDatabase;

    private function creatorProfile(): CreatorProfile
    {
        $user = User::factory()->create(['role' => 'creator']);

        return CreatorProfile::create(['user_id' => $user->id, 'onboarding_completed_at' => now()]);
    }

    public function test_client_starting_a_conversation_creates_one_with_no_project(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $creatorProfile = $this->creatorProfile();

        $response = $this->actingAs($client)->post("/creators/{$creatorProfile->id}/message");

        $conversation = Conversation::firstOrFail();
        $response->assertRedirect(route('messages.show', $conversation));

        $this->assertNull($conversation->project_id);
        $this->assertTrue($conversation->participants->contains('id', $client->id));
        $this->assertTrue($conversation->participants->contains('id', $creatorProfile->user_id));
        $this->assertCount(2, $conversation->participants);
    }

    public function test_starting_a_conversation_twice_reuses_the_same_conversation(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $creatorProfile = $this->creatorProfile();

        $this->actingAs($client)->post("/creators/{$creatorProfile->id}/message");
        $this->actingAs($client)->post("/creators/{$creatorProfile->id}/message");

        $this->assertSame(1, Conversation::count());
    }

    public function test_user_cannot_message_themselves(): void
    {
        $creatorProfile = $this->creatorProfile();

        $response = $this->actingAs($creatorProfile->user)->post("/creators/{$creatorProfile->id}/message");

        $response->assertForbidden();
    }

    public function test_creator_sees_conversation_in_inbox_and_can_reply(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $creatorProfile = $this->creatorProfile();

        $this->actingAs($client)->post("/creators/{$creatorProfile->id}/message");
        $conversation = Conversation::firstOrFail();

        $this->actingAs($client)->post("/messages/{$conversation->id}", [
            'body' => 'Здраво, те интересира ли соработка?',
        ]);

        $inbox = $this->actingAs($creatorProfile->user)->get('/messages');
        $inbox->assertSee($client->name);
        $inbox->assertSee('Здраво, те интересира ли соработка?');

        $reply = $this->actingAs($creatorProfile->user)->post("/messages/{$conversation->id}", [
            'body' => 'Да, слободен сум следната недела.',
        ]);

        $reply->assertRedirect(route('messages.show', $conversation));
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $creatorProfile->user_id,
            'body' => 'Да, слободен сум следната недела.',
        ]);
    }

    public function test_opening_a_conversation_marks_unread_messages_as_read(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $creatorProfile = $this->creatorProfile();

        $this->actingAs($client)->post("/creators/{$creatorProfile->id}/message");
        $conversation = Conversation::firstOrFail();

        $this->actingAs($client)->post("/messages/{$conversation->id}", ['body' => 'Прва порака']);

        $message = Message::where('conversation_id', $conversation->id)->firstOrFail();
        $this->assertNull($message->read_at);

        $this->actingAs($creatorProfile->user)->get("/messages/{$conversation->id}");

        $this->assertNotNull($message->fresh()->read_at);
    }

    public function test_sending_a_message_emails_the_other_participant(): void
    {
        Mail::fake();

        $client = User::factory()->create(['role' => 'client']);
        $creatorProfile = $this->creatorProfile();

        $this->actingAs($client)->post("/creators/{$creatorProfile->id}/message");
        $conversation = Conversation::firstOrFail();

        $this->actingAs($client)->post("/messages/{$conversation->id}", [
            'body' => 'Здраво, те интересира ли соработка?',
        ]);

        Mail::assertSent(NewMessageNotification::class, function ($mail) use ($creatorProfile) {
            return $mail->hasTo($creatorProfile->user->email);
        });
        Mail::assertNotSent(NewMessageNotification::class, function ($mail) use ($client) {
            return $mail->hasTo($client->email);
        });
    }

    public function test_message_notification_is_not_sent_when_recipient_disabled_email_notifications(): void
    {
        Mail::fake();

        $client = User::factory()->create(['role' => 'client']);
        $creatorProfile = $this->creatorProfile();
        $creatorProfile->user->update(['email_notifications_enabled' => false]);

        $this->actingAs($client)->post("/creators/{$creatorProfile->id}/message");
        $conversation = Conversation::firstOrFail();

        $this->actingAs($client)->post("/messages/{$conversation->id}", [
            'body' => 'Здраво, те интересира ли соработка?',
        ]);

        Mail::assertNothingSent();
    }

    public function test_replying_via_the_message_inbox_livewire_component_emails_the_other_participant(): void
    {
        Mail::fake();

        $client = User::factory()->create(['role' => 'client']);
        $creatorProfile = $this->creatorProfile();

        $this->actingAs($client)->post("/creators/{$creatorProfile->id}/message");
        $conversation = Conversation::firstOrFail();

        Livewire::actingAs($client)
            ->test('message-inbox', ['selectedConversationId' => $conversation->id])
            ->set('newMessageBody', 'Здраво, те интересира ли соработка?')
            ->call('sendMessage');

        Mail::assertSent(NewMessageNotification::class, function ($mail) use ($creatorProfile) {
            return $mail->hasTo($creatorProfile->user->email);
        });
    }

    public function test_non_participant_cannot_view_or_reply_to_conversation(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $creatorProfile = $this->creatorProfile();
        $outsider = User::factory()->create(['role' => 'client']);

        $this->actingAs($client)->post("/creators/{$creatorProfile->id}/message");
        $conversation = Conversation::firstOrFail();

        $this->actingAs($outsider)->get("/messages/{$conversation->id}")->assertForbidden();
        $this->actingAs($outsider)->post("/messages/{$conversation->id}", ['body' => 'Test'])->assertForbidden();
    }

    public function test_unread_badge_shows_count_and_clears_after_reading(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $creatorProfile = $this->creatorProfile();

        $this->actingAs($client)->post("/creators/{$creatorProfile->id}/message");
        $conversation = Conversation::firstOrFail();

        $conversation->messages()->create([
            'sender_id' => $creatorProfile->user_id,
            'body' => 'Здраво',
        ]);

        $this->assertSame(1, Livewire::actingAs($client)->test('unread-badge')->instance()->count);

        $this->actingAs($client)->get("/messages/{$conversation->id}");

        $this->assertSame(0, Livewire::actingAs($client)->test('unread-badge')->instance()->count);
    }

    public function test_unread_badge_dispatches_a_sound_event_when_the_unread_count_increases(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $creatorProfile = $this->creatorProfile();

        $this->actingAs($client)->post("/creators/{$creatorProfile->id}/message");
        $conversation = Conversation::firstOrFail();

        $badge = Livewire::actingAs($client)->test('unread-badge');

        $conversation->messages()->create([
            'sender_id' => $creatorProfile->user_id,
            'body' => 'Здраво',
        ]);

        $badge->call('poll')->assertDispatched('new-message-received');
    }

    public function test_unread_badge_does_not_dispatch_a_sound_event_when_the_count_is_unchanged(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        Livewire::actingAs($client)->test('unread-badge')
            ->call('poll')
            ->assertNotDispatched('new-message-received');
    }

    public function test_message_inbox_dispatches_a_sound_event_when_a_new_message_arrives_in_the_open_conversation(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $creatorProfile = $this->creatorProfile();

        $this->actingAs($client)->post("/creators/{$creatorProfile->id}/message");
        $conversation = Conversation::firstOrFail();

        $component = Livewire::actingAs($client)->test('message-inbox', ['selectedConversationId' => $conversation->id]);

        $conversation->messages()->create([
            'sender_id' => $creatorProfile->user_id,
            'body' => 'Здраво',
        ]);

        $component->call('poll')->assertDispatched('new-message-received');
    }

    public function test_message_inbox_does_not_dispatch_a_sound_event_for_the_users_own_message(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $creatorProfile = $this->creatorProfile();

        $this->actingAs($client)->post("/creators/{$creatorProfile->id}/message");
        $conversation = Conversation::firstOrFail();

        $component = Livewire::actingAs($client)->test('message-inbox', ['selectedConversationId' => $conversation->id]);

        $component->set('newMessageBody', 'Здраво')
            ->call('sendMessage')
            ->call('poll')
            ->assertNotDispatched('new-message-received');
    }

    public function test_submitting_a_proposal_starts_a_conversation_with_the_pitch_as_first_message(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $category = \App\Models\Category::first() ?? \App\Models\Category::create(['name' => 'Video', 'slug' => 'video', 'icon' => '🎬']);
        $project = \App\Models\Project::create([
            'client_id' => $client->id,
            'title' => 'Test project',
            'description' => 'Description',
            'status' => 'open',
        ]);
        $project->categories()->attach($category);

        $creatorProfile = $this->creatorProfile();

        $this->actingAs($creatorProfile->user)->post("/projects/{$project->id}/proposals", [
            'message' => 'Ме интересира овој проект.',
            'price' => 250,
        ]);

        $conversation = Conversation::where('project_id', $project->id)->firstOrFail();

        $this->assertTrue($conversation->participants->contains('id', $client->id));
        $this->assertTrue($conversation->participants->contains('id', $creatorProfile->user_id));

        $message = $conversation->messages->first();
        $this->assertSame($creatorProfile->user_id, $message->sender_id);
        $this->assertSame('Ме интересира овој проект.', $message->body);
    }

    public function test_messaging_a_creator_after_their_proposal_reuses_the_proposal_conversation(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $category = \App\Models\Category::first() ?? \App\Models\Category::create(['name' => 'Video', 'slug' => 'video', 'icon' => '🎬']);
        $project = \App\Models\Project::create([
            'client_id' => $client->id,
            'title' => 'Test project',
            'description' => 'Description',
            'status' => 'open',
        ]);
        $project->categories()->attach($category);

        $creatorProfile = $this->creatorProfile();

        $this->actingAs($creatorProfile->user)->post("/projects/{$project->id}/proposals", [
            'message' => 'Ме интересира овој проект.',
            'price' => 250,
        ]);

        $projectConversation = Conversation::where('project_id', $project->id)->firstOrFail();

        $this->actingAs($client)->post("/creators/{$creatorProfile->id}/message");

        $this->assertSame(1, Conversation::count());
        $this->assertSame($projectConversation->id, Conversation::firstOrFail()->id);
    }

    public function test_client_can_accept_a_proposal_directly_from_the_chat(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $category = \App\Models\Category::first() ?? \App\Models\Category::create(['name' => 'Video', 'slug' => 'video', 'icon' => '🎬']);
        $project = \App\Models\Project::create([
            'client_id' => $client->id,
            'title' => 'Test project',
            'description' => 'Description',
            'status' => 'open',
        ]);
        $project->categories()->attach($category);

        $creatorProfile = $this->creatorProfile();

        $this->actingAs($creatorProfile->user)->post("/projects/{$project->id}/proposals", [
            'message' => 'Ме интересира овој проект.',
            'price' => 250,
        ]);

        $conversation = Conversation::where('project_id', $project->id)->firstOrFail();
        $proposal = \App\Models\Proposal::where('project_id', $project->id)->firstOrFail();

        $component = Livewire::actingAs($client)->test('message-inbox', ['selectedConversationId' => $conversation->id]);

        $this->assertSame($proposal->id, $component->instance()->relevantProposal->id);

        $component->call('acceptProposal');

        $this->assertSame('accepted', $proposal->fresh()->status);
        $this->assertSame('in_progress', $project->fresh()->status);
        $this->assertDatabaseHas('contracts', [
            'project_id' => $project->id,
            'proposal_id' => $proposal->id,
            'client_id' => $client->id,
        ]);

        $systemMessage = $conversation->messages()->where('type', 'system')->latest()->first();
        $this->assertNotNull($systemMessage);
        $this->assertStringContainsString('прифатена', $systemMessage->body);
    }

    public function test_client_can_reject_a_proposal_directly_from_the_chat(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $category = \App\Models\Category::first() ?? \App\Models\Category::create(['name' => 'Video', 'slug' => 'video', 'icon' => '🎬']);
        $project = \App\Models\Project::create([
            'client_id' => $client->id,
            'title' => 'Test project',
            'description' => 'Description',
            'status' => 'open',
        ]);
        $project->categories()->attach($category);

        $creatorProfile = $this->creatorProfile();

        $this->actingAs($creatorProfile->user)->post("/projects/{$project->id}/proposals", [
            'message' => 'Ме интересира овој проект.',
            'price' => 250,
        ]);

        $conversation = Conversation::where('project_id', $project->id)->firstOrFail();
        $proposal = \App\Models\Proposal::where('project_id', $project->id)->firstOrFail();

        Livewire::actingAs($client)->test('message-inbox', ['selectedConversationId' => $conversation->id])
            ->call('rejectProposal');

        $this->assertSame('rejected', $proposal->fresh()->status);
        $this->assertSame('open', $project->fresh()->status);
    }

    public function test_creator_does_not_see_accept_reject_buttons_for_their_own_proposal(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $category = \App\Models\Category::first() ?? \App\Models\Category::create(['name' => 'Video', 'slug' => 'video', 'icon' => '🎬']);
        $project = \App\Models\Project::create([
            'client_id' => $client->id,
            'title' => 'Test project',
            'description' => 'Description',
            'status' => 'open',
        ]);
        $project->categories()->attach($category);

        $creatorProfile = $this->creatorProfile();

        $this->actingAs($creatorProfile->user)->post("/projects/{$project->id}/proposals", [
            'message' => 'Ме интересира овој проект.',
            'price' => 250,
        ]);

        $conversation = Conversation::where('project_id', $project->id)->firstOrFail();

        $component = Livewire::actingAs($creatorProfile->user)->test('message-inbox', ['selectedConversationId' => $conversation->id]);

        $this->assertNull($component->instance()->relevantProposal);
    }
}
