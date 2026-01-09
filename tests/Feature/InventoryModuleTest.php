<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Services\Inventory\InventoryService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InventoryModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'tenant']);
        config(['database.connections.tenant' => [
            'driver' => 'sqlite',
            'database' => database_path('test_tenant.sqlite'),
            'prefix' => '',
        ]]);

        if (! file_exists(database_path('test_tenant.sqlite'))) {
            touch(database_path('test_tenant.sqlite'));
        }

        Artisan::call('migrate:fresh', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
        ]);
    }

    protected function tearDown(): void
    {
        if (Schema::connection('tenant')->hasTable('migrations')) {
            Artisan::call('migrate:rollback', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
            ]);
        }

        @unlink(database_path('test_tenant.sqlite'));

        parent::tearDown();
    }

    public function test_create_item_with_initial_stock_creates_movement(): void
    {
        $service = app(InventoryService::class);

        $item = $service->createItemWithInitialStock([
            'nombre' => 'Vitaminas',
            'sku' => 'VIT-01',
            'type' => 'product',
            'sale_price' => 50,
            'cost_price' => 30,
            'inventariable' => true,
            'track_inventory' => true,
            'stock' => 5,
            'cantidad' => 2,
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock' => 5,
        ], 'tenant');

        $this->assertDatabaseHas('inventory_movements', [
            'item_id' => $item->id,
            'movement_type' => 'initial',
        ], 'tenant');
    }

    public function test_register_exit_decrements_stock_and_creates_movement(): void
    {
        $service = app(InventoryService::class);

        $item = Item::create([
            'nombre' => 'Snacks',
            'sku' => 'SNK-01',
            'type' => 'product',
            'sale_price' => 20,
            'cost_price' => 10,
            'inventariable' => true,
            'track_inventory' => true,
            'stock' => 10,
            'cantidad' => 2,
            'valor' => 20,
            'costo' => 10,
        ]);

        $service->addExit($item, 3, ['reference' => 'Venta']);

        $item->refresh();

        $this->assertSame(7.0, (float) $item->stock);
        $this->assertDatabaseHas('inventory_movements', [
            'item_id' => $item->id,
            'movement_type' => 'exit',
        ], 'tenant');
    }

    public function test_exit_cannot_leave_negative_stock_when_tracked(): void
    {
        $service = app(InventoryService::class);

        $item = Item::create([
            'nombre' => 'Antipulgas',
            'sku' => 'ANT-01',
            'type' => 'product',
            'sale_price' => 40,
            'cost_price' => 20,
            'inventariable' => true,
            'track_inventory' => true,
            'stock' => 2,
            'cantidad' => 1,
            'valor' => 40,
            'costo' => 20,
        ]);

        $this->expectException(ValidationException::class);

        $service->addExit($item, 5);
    }

    public function test_status_agotandose_when_stock_below_threshold(): void
    {
        $item = Item::create([
            'nombre' => 'Suplemento',
            'type' => 'product',
            'inventariable' => true,
            'track_inventory' => true,
            'stock' => 3,
            'cantidad' => 5,
            'valor' => 0,
            'costo' => 0,
        ]);

        $this->assertSame('Agotándose', $item->status_label);
    }
}
