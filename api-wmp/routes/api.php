<?php

use App\Http\Controllers\Api\V1\MealPlanController;
use App\Http\Controllers\Api\V1\MealPlanEntryController;
use App\Http\Controllers\Api\V1\RecipeController;
use App\Http\Controllers\Api\V1\ShoppingListController;
use App\Http\Controllers\Api\V1\StapleController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::get('staples', [StapleController::class, 'index'])->name('staples.index');
    Route::get('staples/{staple:slug}', [StapleController::class, 'show'])->name('staples.show');

    Route::get('recipes', [RecipeController::class, 'index'])->name('recipes.index');
    Route::get('recipes/{recipe:slug}', [RecipeController::class, 'show'])->name('recipes.show');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::apiResource('meal-plans', MealPlanController::class)
            ->parameters(['meal-plans' => 'mealPlan']);

        Route::post('meal-plans/{mealPlan}/entries', [MealPlanEntryController::class, 'store'])
            ->name('meal-plans.entries.store');
        Route::delete('meal-plans/{mealPlan}/entries/{mealPlanEntry}', [MealPlanEntryController::class, 'destroy'])
            ->name('meal-plans.entries.destroy');

        Route::get('meal-plans/{mealPlan}/shopping-list', [ShoppingListController::class, 'show'])
            ->name('meal-plans.shopping-list.show');
    });
});
