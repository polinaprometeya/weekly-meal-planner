<?php

namespace App\Services;

use App\Enums\PackNormConfidence;
use App\Models\FoodPackNorm;
use App\Models\MealPlan;
use Illuminate\Support\Collection;

class PackAwareShoppingListService
{
    public function __construct(
        private readonly ShoppingListService $shoppingList,
        private readonly UnitConversionService $unitConversion,
    ) {}

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function build(MealPlan $mealPlan): Collection
    {
        $lines = $this->shoppingList->build($mealPlan);
        $stapleIds = $lines->pluck('staple.id')->all();

        $norms = FoodPackNorm::query()
            ->whereIn('staple_id', $stapleIds)
            ->orderByRaw("CASE confidence WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END")
            ->get()
            ->groupBy('staple_id')
            ->map(fn (Collection $group) => $group->first());

        return $lines->map(function (array $line) use ($norms) {
            $norm = $norms->get($line['staple']->id);
            $packsToBuy = null;
            $packLabel = null;

            if ($norm && (float) $norm->pack_amount > 0) {
                $packAmountInLineUnit = $this->unitConversion->convert(
                    (float) $norm->pack_amount,
                    $norm->pack_unit,
                    $line['staple']->default_unit,
                );
                $packsToBuy = (int) ceil($line['total_amount'] / $packAmountInLineUnit);
                $packLabel = $norm->label_da;
            }

            return [
                'staple' => $line['staple'],
                'total_amount' => round($line['total_amount'], 3),
                'unit' => $line['unit'],
                'pack_norm' => $norm ? [
                    'pack_amount' => $norm->pack_amount,
                    'pack_unit' => $norm->pack_unit,
                    'label_da' => $norm->label_da,
                    'confidence' => $norm->confidence instanceof PackNormConfidence
                        ? $norm->confidence->value
                        : $norm->confidence,
                ] : null,
                'packs_to_buy' => $packsToBuy,
                'pack_label' => $packLabel,
            ];
        });
    }
}
