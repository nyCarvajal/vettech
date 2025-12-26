<?php

namespace Database\Seeders;

use App\Models\HospitalAdministration;
use App\Models\HospitalCharge;
use App\Models\HospitalDay;
use App\Models\HospitalOrder;
use App\Models\HospitalStay;
use App\Models\HospitalVital;
use App\Models\HospitalProgressNote;
use App\Models\Owner;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HospitalDemoSeeder extends Seeder
{
    public function run(): void
    {
        $owner = Owner::first() ?? Owner::factory()->create();
        $patient = Patient::first() ?? Patient::factory()->create(['owner_id' => $owner->id]);
        $user = User::first();

        $stay = HospitalStay::create([
            'patient_id' => $patient->id,
            'owner_id' => $owner->id,
            'admitted_at' => now()->subDays(2),
            'status' => 'active',
            'severity' => 'observation',
            'created_by' => $user?->id ?? 1,
        ]);

        $day1 = HospitalDay::create([
            'stay_id' => $stay->id,
            'date' => Carbon::now()->subDays(2)->toDateString(),
            'day_number' => 1,
        ]);
        $day2 = HospitalDay::create([
            'stay_id' => $stay->id,
            'date' => Carbon::now()->subDay()->toDateString(),
            'day_number' => 2,
        ]);
        $day3 = HospitalDay::create([
            'stay_id' => $stay->id,
            'date' => Carbon::now()->toDateString(),
            'day_number' => 3,
        ]);

        $order1 = HospitalOrder::create([
            'stay_id' => $stay->id,
            'day_id' => $day3->id,
            'type' => 'medication',
            'source' => 'manual',
            'manual_name' => 'Amoxicilina',
            'dose' => '10 mg/kg',
            'route' => 'VO',
            'frequency' => 'c/12h',
            'schedule_json' => ['08:00', '20:00'],
            'start_at' => now()->subDay(),
            'instructions' => 'Administrar con comida',
            'created_by' => $user?->id ?? 1,
        ]);

        $order2 = HospitalOrder::create([
            'stay_id' => $stay->id,
            'day_id' => $day3->id,
            'type' => 'fluid',
            'source' => 'manual',
            'manual_name' => 'Lactato de Ringer',
            'dose' => '20 ml/kg/h',
            'route' => 'IV',
            'frequency' => 'continuo',
            'schedule_json' => [],
            'start_at' => now(),
            'created_by' => $user?->id ?? 1,
        ]);

        foreach ([
            [now()->subHours(6), '50mg', 'done'],
            [now()->subHours(2), '50mg', 'late'],
        ] as [$at, $dose, $status]) {
            HospitalAdministration::create([
                'order_id' => $order1->id,
                'stay_id' => $stay->id,
                'day_id' => $day3->id,
                'administered_at' => $at,
                'dose_given' => $dose,
                'status' => $status,
                'administered_by' => $user?->id ?? 1,
            ]);
        }

        HospitalVital::insert([
            [
                'stay_id' => $stay->id,
                'day_id' => $day1->id,
                'measured_at' => now()->subDays(2),
                'temp' => 38.5,
                'hr' => 90,
                'rr' => 20,
                'spo2' => 98,
                'measured_by' => $user?->id ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stay_id' => $stay->id,
                'day_id' => $day2->id,
                'measured_at' => now()->subDay(),
                'temp' => 38.2,
                'hr' => 88,
                'rr' => 18,
                'spo2' => 97,
                'measured_by' => $user?->id ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stay_id' => $stay->id,
                'day_id' => $day3->id,
                'measured_at' => now()->subHours(2),
                'temp' => 38.0,
                'hr' => 80,
                'rr' => 16,
                'spo2' => 99,
                'measured_by' => $user?->id ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        HospitalProgressNote::insert([
            [
                'stay_id' => $stay->id,
                'day_id' => $day2->id,
                'logged_at' => now()->subDay()->setHour(8),
                'shift' => 'manana',
                'content' => 'Paciente activo, come bien.',
                'author_id' => $user?->id ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stay_id' => $stay->id,
                'day_id' => $day3->id,
                'logged_at' => now()->setHour(8),
                'shift' => 'manana',
                'content' => 'Ligera diarrea, se ajusta dieta.',
                'author_id' => $user?->id ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        HospitalCharge::insert([
            [
                'stay_id' => $stay->id,
                'day_id' => $day1->id,
                'source' => 'service',
                'description' => 'Hospitalización día 1',
                'qty' => 1,
                'unit_price' => 50,
                'total' => 50,
                'created_by' => $user?->id ?? 1,
                'created_at' => now()->subDays(2),
            ],
            [
                'stay_id' => $stay->id,
                'day_id' => $day2->id,
                'source' => 'service',
                'description' => 'Hospitalización día 2',
                'qty' => 1,
                'unit_price' => 50,
                'total' => 50,
                'created_by' => $user?->id ?? 1,
                'created_at' => now()->subDay(),
            ],
            [
                'stay_id' => $stay->id,
                'day_id' => $day3->id,
                'source' => 'manual',
                'description' => 'Fluidoterapia',
                'qty' => 1,
                'unit_price' => 30,
                'total' => 30,
                'created_by' => $user?->id ?? 1,
                'created_at' => now(),
            ],
            [
                'stay_id' => $stay->id,
                'day_id' => $day3->id,
                'source' => 'manual',
                'description' => 'Medicamentos varios',
                'qty' => 1,
                'unit_price' => 40,
                'total' => 40,
                'created_by' => $user?->id ?? 1,
                'created_at' => now(),
            ],
        ]);
    }
}
