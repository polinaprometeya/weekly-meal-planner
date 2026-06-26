<?php

namespace App\Models;

use App\Enums\PackNormConfidence;
use App\Enums\PackNormSource;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'staple_id',
    'pack_amount',
    'pack_unit',
    'label_da',
    'source',
    'confidence',
    'last_verified',
    'notes',
])]
class FoodPackNorm extends Model
{
    protected function casts(): array
    {
        return [
            'pack_amount' => 'decimal:3',
            'source' => PackNormSource::class,
            'confidence' => PackNormConfidence::class,
            'last_verified' => 'date',
        ];
    }

    public function staple(): BelongsTo
    {
        return $this->belongsTo(Staple::class);
    }
}
