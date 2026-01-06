<?php

namespace Database\Factories;

use App\Models\Followup;
use App\Models\Owner;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class FollowupFactory extends Factory
{
    protected $model = Followup::class;

    public function definition(): array
    {
        $patient = Patient::factory()->create();
        $owner = $patient->owner ?? Owner::factory()->create();

        return [
            'tenant_id' => null,
            'patient_id' => $patient->id,
            'owner_id' => $owner->id,
            'patient_snapshot' => $patient->toArray(),
            'owner_snapshot' => $owner->toArray(),
            'followup_at' => now(),
            'performed_by' => $this->faker->name(),
            'reason' => $this->faker->sentence(3),
            'improved_status' => 'unknown',
            'observations' => $this->faker->sentence(),
        ];
    }
}
