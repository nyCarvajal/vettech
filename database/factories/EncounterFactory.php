<?php

namespace Database\Factories;

use App\Models\Encounter;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class EncounterFactory extends Factory
{
    protected $model = Encounter::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'occurred_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'professional' => $this->faker->name(),
            'motivo' => $this->faker->sentence(),
            'diagnostico' => $this->faker->sentence(),
            'plan' => $this->faker->paragraph(),
            'peso' => $this->faker->randomFloat(2, 1, 40),
            'temperatura' => $this->faker->randomFloat(1, 35, 40),
        ];
    }
}
