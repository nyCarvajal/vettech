<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientShowEagerLoadingTest extends TestCase
{
    use RefreshDatabase;

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
