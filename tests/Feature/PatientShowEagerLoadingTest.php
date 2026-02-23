<?php

namespace Tests\Feature;

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientShowEagerLoadingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function patient_show_limits_timeline_to_ten_items_by_default(): void
    {
        $patient = Patient::factory()->create();

        Encounter::factory()->count(12)->create([
            'patient_id' => $patient->id,
        ]);

        $this->actingAs(User::factory()->create());

        $response = $this->get(route('patients.show', $patient));

        $response->assertOk();

        $timeline = $response->viewData('timeline');

        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $timeline);
        $this->assertSame(10, $timeline->perPage());
        $this->assertCount(10, $timeline->items());
        $this->assertTrue($timeline->hasMorePages());
    }

    /** @test */
    public function patient_show_eager_loads_related_models(): void
    {
        $patient = Patient::factory()->create();

        $this->actingAs(User::factory()->create());

        $response = $this->get(route('patients.show', $patient));

        $response->assertOk();
        $viewPatient = $response->viewData('patient');

        $this->assertTrue($viewPatient->relationLoaded('owner'));
        $this->assertTrue($viewPatient->relationLoaded('species'));
        $this->assertTrue($viewPatient->relationLoaded('breed'));
        $this->assertTrue($viewPatient->relationLoaded('lastEncounter'));
    }
}
