<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CreatorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'headline',
        'bio',
        'hourly_rate',
        'project_rate_from',
        'experience_years',
        'remote_ok',
        'verified',
        'avg_response_hours',
        'languages',
        'equipment',
        'instagram_url',
        'facebook_url',
        'website_url',
        'onboarding_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'hourly_rate' => 'decimal:2',
            'project_rate_from' => 'decimal:2',
            'experience_years' => 'integer',
            'remote_ok' => 'boolean',
            'verified' => 'boolean',
            'avg_response_hours' => 'integer',
            'languages' => 'array',
            'equipment' => 'array',
            'onboarding_completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'creator_category');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'creator_skill');
    }

    public function portfolioItems(): HasMany
    {
        return $this->hasMany(PortfolioItem::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(
            Review::class,
            User::class,
            'id',
            'reviewee_id',
            'user_id',
            'id',
        );
    }
}
