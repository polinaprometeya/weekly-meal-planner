<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'recipe_id',
    'staple_id',
    'amount',
    'unit',
    'note',
    'sort_order',
])]
class RecipeIngredient extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:3',
        ];
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function staple(): BelongsTo
    {
        return $this->belongsTo(Staple::class);
    }
}
