<?php

namespace App\Http\Resources;

use App\Models\RecipeIngredient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin RecipeIngredient */
class RecipeIngredientResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'amount' => $this->amount,
            'unit' => $this->unit,
            'note' => $this->note,
            'sort_order' => $this->sort_order,
            'staple' => new StapleResource($this->whenLoaded('staple')),
        ];
    }
}
