<?php

namespace App\Services;

use App\Models\Breed;
use Illuminate\Support\Str;

class BreedService
{
    public function findOrCreateFromName(string $name, int $speciesId): Breed
    {
        $clean = trim(preg_replace('/\s+/', ' ', $name));
        $normalized = Str::lower($clean);
        $displayName = Str::title($normalized);

        return Breed::firstOrCreate(
            ['species_id' => $speciesId, 'normalized_name' => $normalized],
            ['name' => $displayName]
        );
    }
}
