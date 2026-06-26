<?php

namespace Database\Seeders;

use App\Models\FoodPackNorm;
use App\Models\Staple;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class FoodPackNormSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/food_pack_norms.json');
        $norms = json_decode(File::get($path), true, flags: JSON_THROW_ON_ERROR);

        foreach ($norms as $norm) {
            $staple = Staple::query()->where('slug', $norm['staple_slug'])->firstOrFail();

            FoodPackNorm::query()->updateOrCreate(
                [
                    'staple_id' => $staple->id,
                    'pack_amount' => $norm['pack_amount'],
                    'pack_unit' => $norm['pack_unit'],
                ],
                [
                    'label_da' => $norm['label_da'] ?? null,
                    'source' => $norm['source'],
                    'confidence' => $norm['confidence'],
                    'last_verified' => $norm['last_verified'] ?? now()->toDateString(),
                    'notes' => $norm['notes'] ?? null,
                ],
            );
        }
    }
}
