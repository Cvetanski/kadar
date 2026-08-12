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

    /**
     * A pair of users gets one ongoing conversation per project (and one more
     * for $projectId === null, for direct/general contact outside any
     * specific project) — so inviting the same creator to a second project
     * opens its own thread instead of dropping new messages into whatever
     * project the last conversation happened to be about.
     */
    public static function findOrCreateBetween(User $a, User $b, ?int $projectId = null): self
    {
        $conversation = static::where('project_id', $projectId)
            ->whereHas('participants', fn ($q) => $q->where('users.id', $a->id))
            ->whereHas('participants', fn ($q) => $q->where('users.id', $b->id))
            ->withCount('participants')
            ->get()
            ->firstWhere('participants_count', 2);

        if ($conversation) {
            return $conversation;
        }

        $conversation = static::create(['project_id' => $projectId]);
        $conversation->participants()->attach([$a->id, $b->id]);

        return $conversation;
    }
}
