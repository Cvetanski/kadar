<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Spatie\Translatable\HasTranslations;

class Country extends Model
{
    use HasTranslations;

    public array $translatable = ['name'];

    protected $fillable = ['code', 'name'];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * `name` is a translatable JSON column, so ordering alphabetically
     * can't be done reliably in SQL across drivers — sort the collection
     * in PHP by the resolved (current-locale) name instead.
     *
     * @param  array<int, string>  $with
     */
    public static function orderedByName(array $with = []): Collection
    {
        return static::with($with)->get()->sortBy('name')->values();
    }
}
