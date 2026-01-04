<?php

namespace Database\Factories;

use App\Models\TravelCertificate;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<\App\Models\TravelCertificate> */
class TravelCertificateFactory extends Factory
{
    protected $model = TravelCertificate::class;

    public function definition(): array
    {
        return [
            'code' => TravelCertificate::generateCode(),
            'type' => 'national_co',
            'status' => 'draft',
            'clinic_name' => 'Clinica',
            'clinic_nit' => '123',
            'clinic_address' => 'Calle 1',
            'clinic_phone' => '555',
            'clinic_city' => 'Ciudad',
            'vet_name' => 'Dr Vet',
            'vet_license' => 'LIC123',
            'owner_name' => 'Owner',
            'owner_document_number' => '123',
            'pet_name' => 'Mascota',
            'pet_species' => 'dog',
            'travel_departure_date' => now()->toDateString(),
            'clinical_exam_at' => now(),
            'declaration_text' => 'Declaración',
            'language' => 'es',
        ];
    }
}
