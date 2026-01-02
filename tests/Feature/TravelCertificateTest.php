<?php

namespace Tests\Feature;

use App\Models\TravelCertificate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelCertificateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CountrySeeder::class);
        \App\Models\GeoDepartment::create(['id' => 1, 'nombre' => 'Antioquia', 'codigo' => 5]);
        \App\Models\GeoMunicipality::create(['id' => 1, 'departamento_id' => 1, 'codigo' => 5001, 'nombre' => 'Medellín']);
    }

    public function test_store_national_certificate(): void
    {
        $user = User::factory()->create();
        $department = \App\Models\GeoDepartment::first();
        $municipality = $department->municipalities()->first();

        $payload = $this->basePayload([
            'type' => 'national_co',
            'origin_department_id' => $department->id,
            'origin_municipality_id' => $municipality->id,
            'destination_department_id' => $department->id,
            'destination_municipality_id' => $municipality->id,
        ]);

        $response = $this->actingAs($user)->post(route('travel-certificates.store'), $payload);
        $response->assertRedirect();
        $this->assertDatabaseHas('travel_certificates', ['type' => 'national_co', 'owner_name' => 'John Doe']);
    }

    public function test_store_international_certificate(): void
    {
        $user = User::factory()->create();
        $payload = $this->basePayload([
            'type' => 'international',
            'origin_country_code' => 'CO',
            'destination_country_code' => 'US',
            'origin_city' => 'Bogotá',
            'destination_city' => 'Miami',
        ]);

        $response = $this->actingAs($user)->post(route('travel-certificates.store'), $payload);
        $response->assertRedirect();
        $this->assertDatabaseHas('travel_certificates', ['type' => 'international', 'destination_country_code' => 'US']);
    }

    public function test_issue_certificate(): void
    {
        $user = User::factory()->create();
        $certificate = TravelCertificate::factory()->create(['status' => 'draft']);

        $response = $this->actingAs($user)->post(route('travel-certificates.issue', $certificate));
        $response->assertRedirect();
        $this->assertDatabaseHas('travel_certificates', ['id' => $certificate->id, 'status' => 'issued']);
    }

    public function test_pdf_endpoint(): void
    {
        $user = User::factory()->create();
        $certificate = TravelCertificate::factory()->create();

        $response = $this->actingAs($user)->get(route('travel-certificates.pdf', $certificate));
        $response->assertOk();
    }

    private function basePayload(array $override = []): array
    {
        $base = [
            'language' => 'es',
            'clinic_name' => 'Vet',
            'clinic_nit' => '123',
            'clinic_address' => 'Street 1',
            'clinic_phone' => '555',
            'clinic_city' => 'City',
            'vet_name' => 'Dr Vet',
            'vet_license' => 'ABC123',
            'owner_name' => 'John Doe',
            'owner_document_number' => '123',
            'pet_name' => 'Firulais',
            'pet_species' => 'dog',
            'travel_departure_date' => now()->format('Y-m-d'),
            'clinical_exam_at' => now()->format('Y-m-d H:i:s'),
            'declaration_text' => 'Declaracion',
        ];

        return array_merge($base, $override);
    }
}
