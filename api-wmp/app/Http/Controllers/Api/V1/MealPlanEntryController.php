<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreMealPlanEntryRequest;
use App\Http\Resources\MealPlanEntryResource;
use App\Models\MealPlan;
use App\Models\MealPlanEntry;
use Illuminate\Http\Response;

class MealPlanEntryController extends Controller
{
    public function store(StoreMealPlanEntryRequest $request, MealPlan $mealPlan): MealPlanEntryResource
    {
        $this->authorize('update', $mealPlan);

        $entry = $mealPlan->entries()->create($request->validated());
        $entry->load('recipe');

        return new MealPlanEntryResource($entry);
    }

    public function destroy(MealPlan $mealPlan, MealPlanEntry $mealPlanEntry): Response
    {
        $this->authorize('update', $mealPlan);

        abort_unless($mealPlanEntry->meal_plan_id === $mealPlan->id, 404);

        $mealPlanEntry->delete();

        return response()->noContent();
    }
}
