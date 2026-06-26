<?php

namespace App\Models;

use App\Enums\RecipeSourceType;
use App\Scopes\ReviewedScope;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slug',
    'name_da',
    'servings_default',
    'instructions',
    'source_instructions_raw',
    'source_type',
    'source_title',
    'source_url',
    'publication_year',
    'license',
    'attribution_text',
    'prep_minutes',
    'cook_minutes',
    'tags',
    'is_reviewed',
    'reviewed_at',
])]
#[ScopedBy([ReviewedScope::class])]
class Recipe extends Model
{
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'source_type' => RecipeSourceType::class,
            'tags' => 'array',
            'is_reviewed' => 'boolean',
            'reviewed_at' => 'datetime',
        ];
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class)->orderBy('sort_order');
    }

    public function mealPlanEntries(): HasMany
    {
        return $this->hasMany(MealPlanEntry::class);
    }
}
