<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShoppingListResource;
use App\Http\Resources\StapleResource;
use App\Models\MealPlan;
use App\Services\PackAwareShoppingListService;
use App\Services\ShoppingListService;
use Illuminate\Http\Request;

class ShoppingListController extends Controller
{
    public function show(
        Request $request,
        MealPlan $mealPlan,
        ShoppingListService $shoppingList,
        PackAwareShoppingListService $packAwareShoppingList,
    ): ShoppingListResource {
        $this->authorize('view', $mealPlan);

        if ($request->boolean('pack_aware')) {
            $items = $packAwareShoppingList->build($mealPlan)->map(fn (array $line) => [
                'staple' => (new StapleResource($line['staple']))->resolve(),
                'total_amount' => $line['total_amount'],
                'unit' => $line['unit'],
                'pack_norm' => $line['pack_norm'],
                'packs_to_buy' => $line['packs_to_buy'],
                'pack_label' => $line['pack_label'],
            ]);
        } else {
            $items = $shoppingList->build($mealPlan)->map(fn (array $line) => [
                'staple' => (new StapleResource($line['staple']))->resolve(),
                'total_amount' => round($line['total_amount'], 3),
                'unit' => $line['unit'],
            ]);
        }

        return new ShoppingListResource($items);
    }
}
