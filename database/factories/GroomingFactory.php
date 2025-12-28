<?php

namespace Database\Factories;

use App\Models\Grooming;
use App\Models\Owner;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Grooming> */
class GroomingFactory extends Factory
{
    protected $model = Grooming::class;

    public function definition(): array
    {
        $patient = Patient::factory()->create();

        return [
            'patient_id' => $patient->id,
            'owner_id' => $patient->owner_id ?? Owner::factory()->create()->id,
            'scheduled_at' => now()->addHour(),
            'status' => Grooming::STATUS_AGENDADO,
            'needs_pickup' => false,
            'external_deworming' => false,
            'deworming_source' => 'none',
            'service_source' => 'none',
            'created_by' => User::factory()->create()->id,
        ];
    }

    public function inProgress(): self
    {
        return $this->state(fn () => [
            'status' => Grooming::STATUS_EN_PROCESO,
            'started_at' => now(),
        ]);
    }

    public function finished(): self
    {
        return $this->state(fn () => [
            'status' => Grooming::STATUS_FINALIZADO,
            'started_at' => now()->subHour(),
            'finished_at' => now(),
        ]);
    }
}
