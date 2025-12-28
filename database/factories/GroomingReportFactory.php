<?php

namespace Database\Factories;

use App\Models\Grooming;
use App\Models\GroomingReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<GroomingReport> */
class GroomingReportFactory extends Factory
{
    protected $model = GroomingReport::class;

    public function definition(): array
    {
        return [
            'grooming_id' => Grooming::factory(),
            'fleas' => $this->faker->boolean(30),
            'ticks' => $this->faker->boolean(20),
            'skin_issue' => $this->faker->boolean(15),
            'ear_issue' => $this->faker->boolean(10),
            'observations' => $this->faker->sentence(),
            'recommendations' => $this->faker->sentence(),
            'created_by' => User::factory(),
            'created_at' => now(),
        ];
    }
}
