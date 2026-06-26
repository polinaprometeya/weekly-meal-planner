<?php

namespace App\Models;

use App\Enums\MealSlot;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'meal_plan_id',
    'recipe_id',
    'day_of_week',
    'meal_slot',
    'servings_override',
])]
class MealPlanEntry extends Model
{
    protected function casts(): array
    {
        return [
            'meal_slot' => MealSlot::class,
        ];
    }

    public function mealPlan(): BelongsTo
    {
        return $this->belongsTo(MealPlan::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
