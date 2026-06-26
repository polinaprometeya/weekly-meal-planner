<?php

namespace App\Http\Resources;

use App\Models\MealPlanEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin MealPlanEntry */
class MealPlanEntryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'day_of_week' => $this->day_of_week,
            'meal_slot' => $this->meal_slot,
            'servings_override' => $this->servings_override,
            'recipe' => new RecipeResource($this->whenLoaded('recipe')),
        ];
    }
}
