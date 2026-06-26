<?php

namespace App\Models;

use App\Enums\DefaultUnit;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slug',
    'name_da',
    'category',
    'default_unit',
    'notes',
])]
class Staple extends Model
{
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'default_unit' => DefaultUnit::class,
        ];
    }

    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function foodPackNorms(): HasMany
    {
        return $this->hasMany(FoodPackNorm::class);
    }
}
