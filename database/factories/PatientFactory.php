<?php

namespace Database\Factories;

use App\Models\Breed;
use App\Models\Owner;
use App\Models\Patient;
use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        $species = Species::factory()->create();
        $weightUnit = $this->faker->randomElement(['kg', 'g']);
        $rawWeight = $weightUnit === 'g'
            ? $this->faker->numberBetween(500, 40000)
            : $this->faker->randomFloat(2, 1, 40);

        return [
            'owner_id' => Owner::factory(),
            'species_id' => $species->id,
            'breed_id' => Breed::factory()->create(['species_id' => $species->id])->id,
            'nombres' => $this->faker->firstName(),
            'apellidos' => $this->faker->lastName(),
            'sexo' => $this->faker->randomElement(['M', 'F', 'NA']),
            'age_value' => $this->faker->numberBetween(1, 12),
            'age_unit' => $this->faker->randomElement(['years', 'months']),
            'fecha_nacimiento' => $this->faker->dateTimeBetween('-10 years', '-1 year'),
            'color' => $this->faker->safeColorName(),
            'microchip' => $this->faker->optional()->numerify('###-###-###'),
            'peso_actual' => $weightUnit === 'g' ? $rawWeight / 1000 : $rawWeight,
            'weight_unit' => $weightUnit,
            'temperamento' => $this->faker->randomElement(['tranquilo', 'nervioso', 'agresivo', 'miedoso', 'otro']),
            'alergias' => $this->faker->optional()->sentence(),
            'observaciones' => $this->faker->optional()->sentence(),
            'whatsapp' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
        ];
    }
}
