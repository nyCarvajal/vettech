<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Patient;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientBreedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_breed_from_free_text(): void
    {
        $this->actingAs(User::factory()->create());

        $species = Species::factory()->create();
        $owner = Owner::factory()->create();

        $response = $this->post(route('patients.store'), [
            'nombres' => 'Luna',
            'apellidos' => 'Gómez',
            'species_id' => $species->id,
            'breed_name' => '   french   bulldog ',
            'owner_id' => $owner->id,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('breeds', [
            'species_id' => $species->id,
            'normalized_name' => 'french bulldog',
        ]);

        $patient = Patient::firstOrFail();
        $this->assertNotNull($patient->breed_id);
    }

}
