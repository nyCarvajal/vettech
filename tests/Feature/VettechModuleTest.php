<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class VettechModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser(): int
    {
        DB::table('usuarios')->insert([
            'nombre' => 'Test',
            'apellidos' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('secret'),
        ]);
        return (int) DB::getPdo()->lastInsertId();
    }

    public function test_fefo_elije_lote_mas_cercano(): void
    {
        $userId = $this->createUser();
        $product = Product::create([
            'name' => 'Antibiótico',
            'type' => 'med',
            'unit' => 'ml',
            'requires_batch' => true,
            'min_stock' => 0,
            'sale_price' => 10,
        ]);

        $older = Batch::create(['product_id' => $product->id, 'batch_code' => 'L1', 'expires_at' => now()->addDays(5), 'cost' => 1, 'qty_in' => 0, 'qty_out' => 0, 'qty_available' => 2]);
        $newer = Batch::create(['product_id' => $product->id, 'batch_code' => 'L2', 'expires_at' => now()->addDays(2), 'cost' => 1, 'qty_in' => 0, 'qty_out' => 0, 'qty_available' => 1]);

        $service = new InventoryService();
        $picked = $service->fefoPickBatch($product);

        $this->assertEquals($newer->id, $picked->id);
    }

    public function test_no_permite_stock_negativo(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $product = Product::create([
            'name' => 'Suero',
            'type' => 'insumo',
            'unit' => 'ml',
            'requires_batch' => false,
            'min_stock' => 0,
            'sale_price' => 5,
        ]);

        $service = new InventoryService();
        $service->ensureNonNegative($product, 1);
    }

    public function test_requiere_batch_cuando_producto_lo_exige(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $userId = $this->createUser();
        $product = Product::create([
            'name' => 'Vacuna',
            'type' => 'med',
            'unit' => 'ml',
            'requires_batch' => true,
            'min_stock' => 0,
            'sale_price' => 15,
        ]);

        $service = new InventoryService();
        $service->moveStock($product, null, 'out', 1, 'prueba', null, null, (object)['id' => $userId]);
    }

    public function test_permisos_bloquean_acciones_sin_rol(): void
    {
        $userId = $this->createUser();

        Permission::create(['name' => 'inventory.adjust', 'label' => 'Ajuste']);
        $role = Role::create(['name' => 'farmaceutico', 'label' => 'Farmacéutico']);
        // no asignar rol al usuario

        $user = (object)['id' => $userId];
        $this->assertTrue(Gate::forUser($user)->denies('inventory.adjust'));
    }
}
