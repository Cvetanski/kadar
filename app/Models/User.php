<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'role',
        'is_admin',
        'locale',
        'phone',
        'avatar_url',
        'country_id',
        'city_id',
        'email_notifications_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'email_notifications_enabled' => 'boolean',
        ];
    }

    private const AVATAR_GRADIENTS = [
        '#0B6FE0,#7CC4FF',
        '#17A673,#7BE0BC',
        '#F5A524,#FBD08A',
        '#0B6FE0,#17A673',
        '#EC4899,#F9A8D4',
        '#0B6FE0,#5CA8F5',
    ];

    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                if (! $value) {
                    return null;
                }

                // External avatars (e.g. a Google account picture) are stored as full
                // URLs and must be returned as-is, not resolved against local storage.
                if (Str::startsWith($value, ['http://', 'https://'])) {
                    return $value;
                }

                $path = Str::contains($value, '/storage/') ? Str::after($value, '/storage/') : $value;

                // Root-relative on purpose: an absolute URL built from APP_URL would break
                // whenever the app is actually served from a different host/port (e.g. the
                // dev server's port vs whatever APP_URL happens to say).
                return '/'.ltrim(parse_url(Storage::disk('public')->url($path), PHP_URL_PATH), '/');
            },
            set: function (?string $value) {
                if (! $value) {
                    return null;
                }

                return Str::contains($value, '/storage/') ? Str::after($value, '/storage/') : $value;
            },
        );
    }

    protected function initials(): Attribute
    {
        return Attribute::get(function () {
            $parts = collect(preg_split('/\s+/', trim($this->name)))->filter();

            return $parts->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
        });
    }

    protected function avatarGradient(): Attribute
    {
        return Attribute::get(fn () => self::AVATAR_GRADIENTS[$this->id % count(self::AVATAR_GRADIENTS)]);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function creatorProfile(): HasOne
    {
        return $this->hasOne(CreatorProfile::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'client_id');
    }

    public function reviewsGiven(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(CreatorProfile::class, 'favorites');
    }

    public function savedProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'saved_projects');
    }

    public function unreadMessagesCount(): int
    {
        return Message::whereHas('conversation.participants', fn ($q) => $q->where('users.id', $this->id))
            ->where('sender_id', '!=', $this->id)
            ->whereNull('read_at')
            ->count();
    }
}
