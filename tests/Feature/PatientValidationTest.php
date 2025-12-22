<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function species_is_required_when_creating_a_patient(): void
    {
        $owner = Owner::factory()->create();

        $this->actingAs(User::factory()->create());

        $response = $this->post(route('patients.store'), [
            'owner_id' => $owner->id,
            'nombres' => 'Firulais',
        ]);

        $response->assertSessionHasErrors('species_id');
    }
}
