<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $path = resource_path('data/countries.json');
        if (! File::exists($path)) {
            return;
        }

        $countries = json_decode(File::get($path), true);
        foreach ($countries as $country) {
            Country::updateOrCreate(['code2' => $country['code2']], [
                'name_es' => $country['name_es'],
                'name_en' => $country['name_en'],
            ]);
        }
    }
}
