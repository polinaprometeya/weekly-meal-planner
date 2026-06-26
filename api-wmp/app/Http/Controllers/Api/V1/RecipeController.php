<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RecipeController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $recipes = Recipe::query()
            ->orderBy('name_da')
            ->paginate(20);

        return RecipeResource::collection($recipes);
    }

    public function show(Recipe $recipe): RecipeResource
    {
        $recipe->load(['ingredients.staple']);

        return new RecipeResource($recipe);
    }
}
