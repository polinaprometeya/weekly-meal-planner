<?php

namespace App\Services;

use App\Models\MealPlan;
use App\Models\Staple;
use Illuminate\Support\Collection;

class ShoppingListService
{
    public function __construct(
        private readonly UnitConversionService $unitConversion,
    ) {}

    /**
     * @return Collection<int, array{staple: Staple, total_amount: float, unit: string}>
     */
    public function build(MealPlan $mealPlan): Collection
    {
        $mealPlan->load([
            'entries.recipe.ingredients.staple',
        ]);

        $totals = [];

        foreach ($mealPlan->entries as $entry) {
            $recipe = $entry->recipe;
            $servings = $entry->servings_override ?? $recipe->servings_default;
            $scale = $servings / $recipe->servings_default;

            foreach ($recipe->ingredients as $ingredient) {
                $staple = $ingredient->staple;
                $converted = $this->unitConversion->convert(
                    (float) $ingredient->amount * $scale,
                    $ingredient->unit,
                    $staple->default_unit,
                );

                $key = $staple->id;

                if (! isset($totals[$key])) {
                    $totals[$key] = [
                        'staple' => $staple,
                        'total_amount' => 0.0,
                        'unit' => $staple->default_unit->value,
                    ];
                }

                $totals[$key]['total_amount'] += $converted;
            }
        }

        return collect(array_values($totals))
            ->sortBy(fn (array $line) => $line['staple']->name_da)
            ->values();
    }
}
