<?php

namespace App\Http\Resources;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Recipe */
class RecipeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name_da' => $this->name_da,
            'servings_default' => $this->servings_default,
            'instructions' => $this->instructions,
            'source_type' => $this->source_type,
            'source_title' => $this->source_title,
            'source_url' => $this->source_url,
            'publication_year' => $this->publication_year,
            'license' => $this->license,
            'attribution_text' => $this->attribution_text,
            'prep_minutes' => $this->prep_minutes,
            'cook_minutes' => $this->cook_minutes,
            'tags' => $this->tags,
            'ingredients' => RecipeIngredientResource::collection($this->whenLoaded('ingredients')),
        ];
    }
}
