<?php

namespace Database\Seeders;

use App\Models\Staple;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class StapleSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/staples.json');
        $staples = json_decode(File::get($path), true, flags: JSON_THROW_ON_ERROR);

        foreach ($staples as $staple) {
            Staple::query()->updateOrCreate(
                ['slug' => $staple['slug']],
                [
                    'name_da' => $staple['name_da'],
                    'category' => $staple['category'] ?? null,
                    'default_unit' => $staple['default_unit'],
                    'notes' => $staple['notes'] ?? null,
                ],
            );
        }
    }
}
