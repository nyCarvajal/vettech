<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Item;
use App\Models\Owner;
use App\Services\InvoiceService;
use App\Services\Pricing\TaxCalculator;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InvoiceModuleTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'tenant',
            'database.connections.tenant' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ],
        ]);

        DB::connection('tenant')->getPdo();

        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
        ]);
    }

    public function test_crear_factura_descuenta_inventario(): void
    {
        $owner = Owner::factory()->create();
        $item = Item::factory()->create([
            'stock' => 5,
            'track_inventory' => true,
            'type' => 'product',
            'sale_price' => 1000,
        ]);

        $service = new InvoiceService(new TaxCalculator());

        $invoice = $service->createInvoice([
            'owner_id' => $owner->id,
            'user_id' => 1,
            'lines' => [
                [
                    'item_id' => $item->id,
                    'description' => $item->nombre,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'discount_rate' => 0,
                    'tax_rate' => 0,
                    'commission_rate' => 0,
                ],
            ],
            'payments' => [
                ['method' => 'cash', 'amount' => 2000, 'received' => 2000],
            ],
        ]);

        $item->refresh();

        $this->assertEquals(3, $item->stock);
        $this->assertDatabaseHas('inventory_movements', [
            'related_id' => $invoice->id,
            'movement_type' => 'sale',
        ], 'tenant');
    }

    public function test_pago_en_efectivo_calcula_devueltas(): void
    {
        $owner = Owner::factory()->create();
        $item = Item::factory()->create([
            'stock' => 10,
            'track_inventory' => true,
            'type' => 'product',
            'sale_price' => 1500,
        ]);

        $service = new InvoiceService(new TaxCalculator());

        $invoice = $service->createInvoice([
            'owner_id' => $owner->id,
            'user_id' => 1,
            'lines' => [
                [
                    'item_id' => $item->id,
                    'description' => $item->nombre,
                    'quantity' => 1,
                    'unit_price' => 1500,
                    'discount_rate' => 0,
                    'tax_rate' => 0,
                    'commission_rate' => 0,
                ],
            ],
            'payments' => [
                ['method' => 'cash', 'amount' => 1500, 'received' => 2000],
            ],
        ]);

        $this->assertEquals(500, $invoice->change_total);
        $this->assertDatabaseHas('invoice_payments', [
            'invoice_id' => $invoice->id,
            'change' => 500,
        ], 'tenant');
    }

    public function test_anular_reversa_inventario(): void
    {
        $owner = Owner::factory()->create();
        $item = Item::factory()->create([
            'stock' => 4,
            'track_inventory' => true,
            'type' => 'product',
            'sale_price' => 800,
        ]);

        $service = new InvoiceService(new TaxCalculator());

        $invoice = $service->createInvoice([
            'owner_id' => $owner->id,
            'user_id' => 1,
            'lines' => [
                [
                    'item_id' => $item->id,
                    'description' => $item->nombre,
                    'quantity' => 2,
                    'unit_price' => 800,
                    'discount_rate' => 0,
                    'tax_rate' => 0,
                    'commission_rate' => 0,
                ],
            ],
            'payments' => [
                ['method' => 'cash', 'amount' => 1600, 'received' => 1600],
            ],
        ]);

        $invoice->refresh();
        $service->voidInvoice($invoice, 'Cliente canceló.');

        $item->refresh();

        $this->assertEquals(4, $item->stock);
        $this->assertEquals('void', Invoice::find($invoice->id)->status);
        $this->assertDatabaseHas('inventory_movements', [
            'related_id' => $invoice->id,
            'movement_type' => 'sale_void',
        ], 'tenant');
    }
}
