<?php

namespace Tests\Feature;

use App\Models\Breed;
use App\Models\Owner;
use App\Models\Patient;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientTutorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_and_links_a_new_tutor(): void
    {
        $this->actingAs(User::factory()->create());

        $species = Species::factory()->create();
        $breed = Breed::factory()->create(['species_id' => $species->id]);

        $payload = [
            [
                'nombres' => 'Laura',
                'apellidos' => 'Gómez',
                'documento' => '123456',
                'telefono' => '3001234567',
                'email' => 'laura@example.com',
                'direccion' => 'Calle 123',
                'ciudad' => 'Bogotá',
                'parentesco' => 'Propietaria',
                'es_principal' => true,
            ],
        ];

        $response = $this->post(route('patients.store'), [
            'nombres' => 'Max',
            'species_id' => $species->id,
            'breed_id' => $breed->id,
            'tutores_json' => json_encode($payload),
        ]);

        $response->assertRedirect();

        $owner = Owner::firstOrFail();
        $patient = Patient::firstOrFail();

        $this->assertDatabaseHas('patient_owner', [
            'patient_id' => $patient->id,
            'owner_id' => $owner->id,
            'is_primary' => true,
        ]);

        $this->assertSame($owner->id, $patient->owner_id);
    }
}
