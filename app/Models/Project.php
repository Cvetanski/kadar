<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'client_id',
        'title',
        'description',
        'budget_min',
        'budget_max',
        'deadline',
        'country_id',
        'city_id',
        'remote_ok',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'budget_min' => 'decimal:2',
            'budget_max' => 'decimal:2',
            'deadline' => 'date',
            'remote_ok' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'project_category');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'project_skill');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    /**
     * Free-plan clients can only receive this many proposals per project;
     * past that, creators can no longer apply until the client upgrades.
     * Pro and legacy-free clients are unlimited.
     */
    public const FREE_PROPOSAL_LIMIT = 5;

    public function hasReachedFreeProposalLimit(): bool
    {
        if ($this->client->hasActiveSubscription()) {
            return false;
        }

        return $this->proposals()->count() >= self::FREE_PROPOSAL_LIMIT;
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
}
