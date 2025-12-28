<?php

namespace Database\Seeders;

use App\Models\Grooming;
use App\Models\GroomingReport;
use App\Models\Patient;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class GroomingDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create();

        $serviceProduct = Product::firstOrCreate([
            'name' => 'Baño y corte',
            'type' => 'servicio',
        ], [
            'sku' => 'GRM-001',
            'unit' => 'serv',
            'requires_batch' => false,
            'min_stock' => 0,
            'sale_price' => 45000,
            'cost_avg' => 0,
            'active' => true,
        ]);

        $dewormingProduct = Product::firstOrCreate([
            'name' => 'Pipeta antipulgas',
            'type' => 'med',
        ], [
            'sku' => 'ANT-001',
            'unit' => 'ud',
            'requires_batch' => false,
            'min_stock' => 0,
            'sale_price' => 20000,
            'cost_avg' => 0,
            'active' => true,
        ]);

        $patients = Patient::factory()->count(6)->create();

        // 5 agendados hoy
        foreach (range(1, 5) as $i) {
            $patient = $patients->get(($i - 1) % $patients->count());
            Grooming::factory()->create([
                'patient_id' => $patient->id,
                'owner_id' => $patient->owner_id,
                'scheduled_at' => now()->setTime(8 + $i, 0),
                'needs_pickup' => $i <= 2,
                'pickup_address' => $i <= 2 ? 'Calle ' . (10 + $i) . ' #12-34' : null,
                'external_deworming' => $i <= 3,
                'deworming_source' => $i <= 3 ? 'inventory' : 'none',
                'deworming_product_id' => $i <= 3 ? $dewormingProduct->id : null,
                'product_service_id' => $serviceProduct->id,
                'service_source' => 'product',
                'service_price' => $serviceProduct->sale_price,
                'created_by' => $user->id,
            ]);
        }

        // 3 en proceso
        foreach (range(1, 3) as $i) {
            $patient = $patients->get($i % $patients->count());
            Grooming::factory()->inProgress()->create([
                'patient_id' => $patient->id,
                'owner_id' => $patient->owner_id,
                'scheduled_at' => now()->setTime(10 + $i, 30),
                'product_service_id' => $serviceProduct->id,
                'service_source' => 'product',
                'service_price' => $serviceProduct->sale_price,
                'created_by' => $user->id,
            ]);
        }

        // 5 finalizados con informe
        foreach (range(1, 5) as $i) {
            $patient = $patients->get(($i + 1) % $patients->count());
            $grooming = Grooming::factory()->finished()->create([
                'patient_id' => $patient->id,
                'owner_id' => $patient->owner_id,
                'scheduled_at' => now()->subDay()->setTime(9 + $i, 15),
                'product_service_id' => $serviceProduct->id,
                'service_source' => 'product',
                'service_price' => $serviceProduct->sale_price,
                'created_by' => $user->id,
            ]);

            GroomingReport::factory()->create([
                'grooming_id' => $grooming->id,
                'created_by' => $user->id,
                'observations' => 'Paciente tranquilo, sin complicaciones.',
            ]);
        }
    }
}
