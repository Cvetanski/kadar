<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Skill extends Model
{
    use HasTranslations;

    public array $translatable = ['name'];

    protected $fillable = ['name', 'slug'];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'skill_category');
    }

    public function creatorProfiles(): BelongsToMany
    {
        return $this->belongsToMany(CreatorProfile::class, 'creator_skill');
    }
}
