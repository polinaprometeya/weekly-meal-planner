<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\Staple;
use App\Scopes\ReviewedScope;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $directory = database_path('seeders/data/recipes');
        $files = File::glob($directory.'/*.json') ?: [];

        foreach ($files as $file) {
            $data = json_decode(File::get($file), true, flags: JSON_THROW_ON_ERROR);
            $ingredients = $data['ingredients'];
            unset($data['ingredients']);

            $isReviewed = (bool) ($data['is_reviewed'] ?? false);
            $data['reviewed_at'] = $isReviewed ? now() : null;

            $recipe = Recipe::withoutGlobalScope(ReviewedScope::class)->updateOrCreate(
                ['slug' => $data['slug']],
                $data,
            );

            $recipe->ingredients()->delete();

            foreach ($ingredients as $ingredient) {
                $staple = Staple::query()->where('slug', $ingredient['staple_slug'])->firstOrFail();

                RecipeIngredient::query()->create([
                    'recipe_id' => $recipe->id,
                    'staple_id' => $staple->id,
                    'amount' => $ingredient['amount'],
                    'unit' => $ingredient['unit'],
                    'note' => $ingredient['note'] ?? null,
                    'sort_order' => $ingredient['sort_order'] ?? 0,
                ]);
            }
        }
    }
}
