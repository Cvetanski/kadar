<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

class CreatorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'slug',
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
        'onboarding_skipped_at',
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
            'onboarding_skipped_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CreatorProfile $profile) {
            if (! $profile->slug) {
                $profile->slug = static::generateUniqueSlug($profile->user?->name ?? 'creator');
            }
        });
    }

    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'creator';
        $slug = $base;
        $suffix = 2;

        while (
            static::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    /**
     * Falls back to the numeric id for models created before slugs existed,
     * so route() generation never produces a broken URL.
     */
    public function getRouteKey(): string
    {
        return $this->slug ?? (string) $this->id;
    }

    public function resolveRouteBinding($value, $field = null): ?self
    {
        return static::where('slug', $value)
            ->when(ctype_digit((string) $value), fn ($query) => $query->orWhere('id', $value))
            ->first();
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
        return $this->hasMany(PortfolioItem::class)->orderBy('sort_order');
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
