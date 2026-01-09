<?php

namespace Tests\Feature\Reports;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.tenant.database' => null,
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::connection('sqlite')->create('invoices', function ($table) {
            $table->id();
            $table->string('full_number');
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('user_id');
            $table->string('status');
            $table->dateTime('issued_at');
            $table->decimal('total', 14, 2)->default(0);
            $table->decimal('paid_total', 14, 2)->default(0);
        });

        Schema::connection('sqlite')->create('owners', function ($table) {
            $table->id();
            $table->string('name');
        });

        DB::connection('sqlite')->table('owners')->insert(['id' => 1, 'name' => 'Cliente Demo']);
        DB::connection('sqlite')->table('invoices')->insert([
            'full_number' => 'POS-001',
            'owner_id' => 1,
            'user_id' => 1,
            'status' => 'issued',
            'issued_at' => now(),
            'total' => 10000,
            'paid_total' => 5000,
        ]);
    }

    public function test_it_exports_sales_csv(): void
    {
        $user = new User();
        $user->id = 1;
        $user->role = 'admin';

        $response = $this->actingAs($user)->get('/reports/export?report=sales&format=csv');

        $response->assertOk();
        $this->assertStringContainsString('Factura,Cliente,Estado', $response->streamedContent());
    }
}
