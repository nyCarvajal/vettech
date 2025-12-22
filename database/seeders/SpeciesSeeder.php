<?php

namespace Database\Seeders;

use App\Models\Breed;
use App\Models\Species;
use Illuminate\Database\Seeder;

class SpeciesSeeder extends Seeder
{
    public function run(): void
    {
        $speciesData = [
            'Perro' => ['Labrador Retriever', 'Pastor Alemán', 'Bulldog', 'Criollo'],
            'Gato' => ['Persa', 'Siames', 'Bengalí', 'Doméstico'],
            'Conejo' => ['Rex', 'Cabeza de león'],
        ];

        foreach ($speciesData as $speciesName => $breeds) {
            $species = Species::firstOrCreate(['name' => $speciesName]);
            foreach ($breeds as $breed) {
                Breed::firstOrCreate(['name' => $breed, 'species_id' => $species->id]);
            }
        }
    }
}
