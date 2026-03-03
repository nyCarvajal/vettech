<?php

namespace Tests\Feature;

use App\Models\HospitalDay;
use App\Models\HospitalOrder;
use App\Models\HospitalStay;
use App\Models\Owner;
use App\Models\Patient;
use App\Models\Product;
use App\Models\User;
use App\Services\HospitalTreatmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HospitalApplyOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_aplicar_orden_crea_application_actualiza_next_due_y_crea_charge(): void
    {
        $patient = Patient::factory()->create();
        $owner = Owner::factory()->create();
        $user = User::factory()->create(['role' => 'medico']);
        $product = Product::factory()->create(['sale_price' => 15000]);

        $stay = HospitalStay::create([
            'patient_id' => $patient->id,
            'owner_id' => $owner->id,
            'admitted_at' => now(),
            'severity' => 'stable',
            'created_by' => $user->id,
        ]);

        $day = HospitalDay::create([
            'stay_id' => $stay->id,
            'date' => now()->toDateString(),
            'day_number' => 1,
        ]);

        $order = HospitalOrder::create([
            'stay_id' => $stay->id,
            'day_id' => $day->id,
            'type' => 'medication',
            'source' => 'inventory',
            'product_id' => $product->id,
            'dose' => '1 tab',
            'route' => 'VO',
            'frequency_type' => 'q_hours',
            'frequency_value' => 8,
            'start_at' => now()->subHour(),
            'next_due_at' => now(),
            'created_by' => $user->id,
        ]);

        $service = app(HospitalTreatmentService::class);
        $service->createAdministration($order, [
            'administered_at' => now(),
            'dose_given' => '1 tab',
            'status' => 'done',
            'notes' => null,
            'administered_by' => $user->id,
            'is_admin' => false,
        ]);

        $this->assertDatabaseHas('hospital_administrations', [
            'order_id' => $order->id,
            'dose_given' => '1 tab',
        ]);

        $this->assertNotNull($order->fresh()->next_due_at);

        $this->assertDatabaseHas('hospital_charges', [
            'stay_id' => $stay->id,
            'order_id' => $order->id,
            'status' => 'pending',
            'unit_price' => 15000,
        ]);
    }
}
