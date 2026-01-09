<?php

namespace Tests\Feature;

use App\Models\CashClosure;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Owner;
use App\Services\CashClosureService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CashClosureTest extends TestCase
{
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

    public function test_summary_returns_expected_totals(): void
    {
        $owner = Owner::factory()->create();
        $invoice = Invoice::create([
            'invoice_type' => 'pos',
            'prefix' => 'A',
            'number' => 1,
            'full_number' => 'A-1',
            'owner_id' => $owner->id,
            'user_id' => 1,
            'status' => 'paid',
            'currency' => 'COP',
            'issued_at' => now(),
        ]);

        InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'method' => 'cash',
            'amount' => 150000,
            'received' => 150000,
            'change' => 0,
            'paid_at' => Carbon::parse('2026-01-12 10:00:00'),
        ]);

        InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'method' => 'card',
            'amount' => 50000,
            'received' => 50000,
            'change' => 0,
            'paid_at' => Carbon::parse('2026-01-12 12:00:00'),
        ]);

        $service = new CashClosureService();
        $summary = $service->getSummary('2026-01-12');

        $this->assertSame(150000.0, $summary['expected']['cash']);
        $this->assertSame(50000.0, $summary['expected']['card']);
        $this->assertSame(200000.0, $summary['expected']['total']);
    }

    public function test_store_closure_calculates_difference(): void
    {
        $service = new CashClosureService();
        $summary = [
            'date' => '2026-01-12',
            'expected' => [
                'cash' => 100000,
                'card' => 50000,
                'transfer' => 0,
                'total' => 150000,
            ],
            'expected_by_method' => [],
            'expenses' => [
                'total' => 0,
                'by_method' => [],
                'available' => false,
                'items' => [],
            ],
            'net' => 150000,
        ];

        $closure = $service->storeClosure([
            'counted_cash' => 90000,
            'counted_card' => 50000,
            'counted_transfer' => 0,
            'notes' => 'Cierre de prueba',
        ], $summary, 1);

        $this->assertSame(-10000.0, (float) $closure->difference);
        $this->assertDatabaseHas('cash_closures', [
            'date' => '2026-01-12',
            'total_counted' => 140000,
        ], 'tenant');
    }

    public function test_store_closure_upserts_by_date(): void
    {
        $service = new CashClosureService();
        $summary = [
            'date' => '2026-01-12',
            'expected' => [
                'cash' => 100000,
                'card' => 0,
                'transfer' => 0,
                'total' => 100000,
            ],
            'expected_by_method' => [],
            'expenses' => [
                'total' => 0,
                'by_method' => [],
                'available' => false,
                'items' => [],
            ],
            'net' => 100000,
        ];

        $service->storeClosure([
            'counted_cash' => 100000,
            'counted_card' => 0,
            'counted_transfer' => 0,
        ], $summary, 1);

        $service->storeClosure([
            'counted_cash' => 95000,
            'counted_card' => 0,
            'counted_transfer' => 0,
        ], $summary, 1);

        $this->assertSame(1, CashClosure::count());
        $this->assertDatabaseHas('cash_closures', [
            'date' => '2026-01-12',
            'counted_cash' => 95000,
        ], 'tenant');
    }
}
