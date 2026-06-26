<?php

namespace App\Http\Resources;

use App\Models\Staple;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Staple */
class StapleResource extends JsonResource
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
            'category' => $this->category,
            'default_unit' => $this->default_unit,
        ];
    }
}
