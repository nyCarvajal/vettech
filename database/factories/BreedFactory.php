<?php

namespace Database\Factories;

use App\Models\Breed;
use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

class BreedFactory extends Factory
{
    protected $model = Breed::class;

    public function definition(): array
    {
        $name = $this->faker->word();

        return [
            'species_id' => Species::factory(),
            'name' => $name,
            'normalized_name' => strtolower($name),
        ];
    }
}
