<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    protected $fillable = ['project_id'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public static function findOrCreateBetween(User $a, User $b, ?int $projectId = null): self
    {
        // A pair of users only ever has one ongoing conversation, regardless of
        // which project (if any) it was originally started from — otherwise
        // actions like "message this creator" from different pages (a project's
        // proposal list, a creator's profile, an application) would each spawn
        // their own duplicate thread between the same two people.
        $conversation = static::whereHas('participants', fn ($q) => $q->where('users.id', $a->id))
            ->whereHas('participants', fn ($q) => $q->where('users.id', $b->id))
            ->withCount('participants')
            ->get()
            ->firstWhere('participants_count', 2);

        if ($conversation) {
            if ($projectId !== null && $conversation->project_id !== $projectId) {
                $conversation->update(['project_id' => $projectId]);
            }

            return $conversation;
        }

        $conversation = static::create(['project_id' => $projectId]);
        $conversation->participants()->attach([$a->id, $b->id]);

        return $conversation;
    }
}
