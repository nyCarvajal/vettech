<?php

namespace Tests\Feature;

use App\Models\HospitalDay;
use App\Models\HospitalOrder;
use App\Models\HospitalStay;
use App\Models\Owner;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\HospitalBillingService;
use App\Services\HospitalStayService;
use App\Services\HospitalTreatmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HospitalModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_crea_stay_crea_day1(): void
    {
        $patient = Patient::factory()->create();
        $owner = Owner::factory()->create();
        $user = User::factory()->create();

        $service = new HospitalStayService();
        $stay = $service->admit([
            'patient_id' => $patient->id,
            'owner_id' => $owner->id,
            'admitted_at' => now(),
            'severity' => 'stable',
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('hospital_days', [
            'stay_id' => $stay->id,
            'day_number' => 1,
        ]);
    }

    public function test_ensureDays_crea_dias_hasta_hoy(): void
    {
        $patient = Patient::factory()->create();
        $owner = Owner::factory()->create();
        $user = User::factory()->create();

        $stay = HospitalStay::create([
            'patient_id' => $patient->id,
            'owner_id' => $owner->id,
            'admitted_at' => now()->subDays(2),
            'severity' => 'stable',
            'created_by' => $user->id,
        ]);

        $service = new HospitalStayService();
        $service->ensureDays($stay);

        $this->assertEquals(3, $stay->fresh()->days()->count());
    }

    public function test_order_manual_requiere_nombre(): void
    {
        $this->expectExceptionMessage('El nombre es requerido para órdenes manuales.');
        $service = new HospitalTreatmentService();
        $service->createOrder([
            'stay_id' => 1,
            'type' => 'medication',
            'source' => 'manual',
            'start_at' => now(),
            'created_by' => User::factory()->create()->id,
        ]);
    }

    public function test_order_inventory_requiere_product_id(): void
    {
        $this->expectExceptionMessage('El producto es requerido para órdenes desde inventario.');
        $service = new HospitalTreatmentService();
        $service->createOrder([
            'stay_id' => 1,
            'type' => 'medication',
            'source' => 'inventory',
            'start_at' => now(),
            'created_by' => User::factory()->create()->id,
        ]);
    }

    public function test_administra_dosis_registra_historial(): void
    {
        $patient = Patient::factory()->create();
        $owner = Owner::factory()->create();
        $user = User::factory()->create();
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
            'source' => 'manual',
            'manual_name' => 'Test',
            'start_at' => now(),
            'created_by' => $user->id,
        ]);

        $service = new HospitalTreatmentService();
        $service->createAdministration($order, [
            'administered_at' => now(),
            'dose_given' => '5ml',
            'status' => 'done',
            'administered_by' => $user->id,
        ]);

        $this->assertDatabaseHas('hospital_administrations', [
            'order_id' => $order->id,
            'dose_given' => '5ml',
        ]);
    }

    public function test_generate_invoice_crea_sale_y_items(): void
    {
        $patient = Patient::factory()->create();
        $owner = Owner::factory()->create();
        $user = User::factory()->create();
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
        $stay->charges()->create([
            'day_id' => $day->id,
            'source' => 'manual',
            'description' => 'Prueba',
            'qty' => 2,
            'unit_price' => 10,
            'total' => 20,
            'created_by' => $user->id,
            'created_at' => now(),
        ]);

        $billing = new HospitalBillingService();
        $sale = $billing->generateInvoice($stay->fresh('charges'));

        $this->assertInstanceOf(Sale::class, $sale);
        $this->assertEquals(20, $sale->total);
        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $sale->id,
            'qty' => 2,
        ]);
    }
}
