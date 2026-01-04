<?php

namespace Database\Seeders;

use App\Models\GeoDepartment;
use App\Models\GeoMunicipality;
use Illuminate\Database\Seeder;

class GeoMunicipalitySeeder extends Seeder
{
    public function run(): void
    {
        $pairs = [
            'Bogotá D.C.' => ['Bogotá D.C.'],
            'Antioquia' => ['Medellín'],
            'Valle del Cauca' => ['Cali'],
            'Atlántico' => ['Barranquilla'],
            'Bolívar' => ['Cartagena'],
        ];

        foreach ($pairs as $departmentName => $municipalities) {
            $department = GeoDepartment::firstOrCreate(['name' => $departmentName]);
            foreach ($municipalities as $municipality) {
                GeoMunicipality::firstOrCreate([
                    'geo_department_id' => $department->id,
                    'name' => $municipality,
                ]);
            }
        }
    }
}
