<?php

namespace Database\Seeders;

use App\Models\GeoDepartment;
use Illuminate\Database\Seeder;

class GeoDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Amazonas'], ['name' => 'Antioquia'], ['name' => 'Arauca'], ['name' => 'Atlántico'],
            ['name' => 'Bogotá D.C.'], ['name' => 'Bolívar'], ['name' => 'Boyacá'], ['name' => 'Caldas'],
            ['name' => 'Caquetá'], ['name' => 'Casanare'], ['name' => 'Cauca'], ['name' => 'Cesar'],
            ['name' => 'Chocó'], ['name' => 'Córdoba'], ['name' => 'Cundinamarca'], ['name' => 'Guainía'],
            ['name' => 'Guaviare'], ['name' => 'Huila'], ['name' => 'La Guajira'], ['name' => 'Magdalena'],
            ['name' => 'Meta'], ['name' => 'Nariño'], ['name' => 'Norte de Santander'], ['name' => 'Putumayo'],
            ['name' => 'Quindío'], ['name' => 'Risaralda'], ['name' => 'San Andrés y Providencia'], ['name' => 'Santander'],
            ['name' => 'Sucre'], ['name' => 'Tolima'], ['name' => 'Valle del Cauca'], ['name' => 'Vaupés'],
            ['name' => 'Vichada'],
        ];

        foreach ($departments as $dept) {
            GeoDepartment::firstOrCreate(['name' => $dept['name']], $dept);
        }
    }
}
