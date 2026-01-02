<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SpeciesSeeder::class,
            ClinicaImportSeeder::class,
            VettechSeeder::class,
            GroomingDemoSeeder::class,
            CountrySeeder::class,
            GeoDepartmentSeeder::class,
            GeoMunicipalitySeeder::class,
        ]);
    }
}
