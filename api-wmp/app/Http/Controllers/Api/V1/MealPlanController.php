<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreMealPlanRequest;
use App\Http\Requests\Api\V1\UpdateMealPlanRequest;
use App\Http\Resources\MealPlanResource;
use App\Models\MealPlan;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class MealPlanController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $mealPlans = MealPlan::query()
            ->where('user_id', auth()->id())
            ->with(['entries.recipe'])
            ->orderByDesc('start_date')
            ->get();

        return MealPlanResource::collection($mealPlans);
    }

    public function store(StoreMealPlanRequest $request): MealPlanResource
    {
        $this->authorize('create', MealPlan::class);

        $mealPlan = MealPlan::query()->create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        return new MealPlanResource($mealPlan);
    }

    public function show(MealPlan $mealPlan): MealPlanResource
    {
        $this->authorize('view', $mealPlan);

        $mealPlan->load(['entries.recipe']);

        return new MealPlanResource($mealPlan);
    }

    public function update(UpdateMealPlanRequest $request, MealPlan $mealPlan): MealPlanResource
    {
        $this->authorize('update', $mealPlan);

        $mealPlan->update($request->validated());
        $mealPlan->load(['entries.recipe']);

        return new MealPlanResource($mealPlan);
    }

    public function destroy(MealPlan $mealPlan): Response
    {
        $this->authorize('delete', $mealPlan);

        $mealPlan->delete();

        return response()->noContent();
    }
}
