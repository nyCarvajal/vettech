<?php

namespace Tests\Feature;

use App\Models\Grooming;
use App\Models\Patient;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroomingModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_store_crea_grooming_agendado(): void
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();

        $payload = [
            'patient_id' => $patient->id,
            'owner_id' => $patient->owner_id,
            'scheduled_at' => now()->addHour()->format('Y-m-d H:i:s'),
            'service_source' => 'none',
        ];

        $response = $this->actingAs($user)->post(route('groomings.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('groomings', [
            'patient_id' => $patient->id,
            'status' => Grooming::STATUS_AGENDADO,
        ]);
    }

    public function test_start_cambia_estado_en_proceso(): void
    {
        $user = User::factory()->create();
        $grooming = Grooming::factory()->create(['status' => Grooming::STATUS_AGENDADO]);

        $response = $this->actingAs($user)->post(route('groomings.start', $grooming));

        $response->assertRedirect();
        $this->assertDatabaseHas('groomings', [
            'id' => $grooming->id,
            'status' => Grooming::STATUS_EN_PROCESO,
        ]);
    }

    public function test_store_report_finaliza_y_setea_finished_at(): void
    {
        $user = User::factory()->create();
        $grooming = Grooming::factory()->create(['status' => Grooming::STATUS_EN_PROCESO]);

        $response = $this->actingAs($user)->post(route('groomings.report.store', $grooming), [
            'fleas' => true,
            'ticks' => false,
            'observations' => 'Todo bien',
        ]);

        $response->assertRedirect();
        $grooming->refresh();

        $this->assertEquals(Grooming::STATUS_FINALIZADO, $grooming->status);
        $this->assertNotNull($grooming->finished_at);
        $this->assertDatabaseHas('grooming_reports', [
            'grooming_id' => $grooming->id,
            'observations' => 'Todo bien',
        ]);
    }

    public function test_needs_pickup_requiere_direccion(): void
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();

        $response = $this->actingAs($user)->post(route('groomings.store'), [
            'patient_id' => $patient->id,
            'owner_id' => $patient->owner_id,
            'scheduled_at' => now()->format('Y-m-d H:i:s'),
            'needs_pickup' => true,
        ]);

        $response->assertSessionHasErrors(['pickup_address']);
    }

    public function test_deworming_inventory_requiere_product_id(): void
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();
        Product::create([
            'name' => 'Servicio prueba',
            'type' => 'servicio',
            'sku' => 'SERV-01',
            'unit' => 'serv',
            'requires_batch' => false,
            'min_stock' => 0,
            'sale_price' => 10000,
            'cost_avg' => 0,
            'active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('groomings.store'), [
            'patient_id' => $patient->id,
            'owner_id' => $patient->owner_id,
            'scheduled_at' => now()->format('Y-m-d H:i:s'),
            'external_deworming' => true,
            'deworming_source' => 'inventory',
        ]);

        $response->assertSessionHasErrors(['deworming_product_id']);
    }
}
