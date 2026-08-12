<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectInvitation extends Model
{
    protected $fillable = [
        'project_id',
        'creator_profile_id',
        'client_id',
        'message',
        'status',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creatorProfile(): BelongsTo
    {
        return $this->belongsTo(CreatorProfile::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Open (or reuse) the conversation for this invitation and post the
     * invitation announcement as the first message — the conversation is
     * live and two-way immediately, there's no separate accept step. The
     * announcement message is stored with type 'invitation' so the chat UI
     * renders it as a styled card instead of a plain bubble, pulling the
     * live project details (title/budget/location) from the conversation's
     * project relation at render time rather than freezing them here.
     */
    public function send(): Conversation
    {
        $creator = $this->creatorProfile->user;
        $conversation = Conversation::findOrCreateBetween($creator, $this->client, $this->project_id);

        $conversation->messages()->create([
            'sender_id' => $this->client_id,
            'body' => __('Покана за проект „:title“', ['title' => $this->project->title]),
            'type' => 'invitation',
        ]);

        if ($this->message) {
            $conversation->messages()->create([
                'sender_id' => $this->client_id,
                'body' => $this->message,
            ]);
        }

        $conversation->touch();

        return $conversation;
    }
}
