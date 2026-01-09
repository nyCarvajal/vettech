<?php

namespace Tests\Feature\Reports;

use App\Reports\PaymentsReportRepository;
use App\Reports\ReportFilters;
use App\Reports\SalesReportRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ReportKpisTest extends TestCase
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
            $table->decimal('commission_total', 14, 2)->default(0);
        });

        Schema::connection('sqlite')->create('invoice_payments', function ($table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->string('method');
            $table->decimal('amount', 14, 2)->default(0);
            $table->dateTime('paid_at');
        });

        Schema::connection('sqlite')->create('owners', function ($table) {
            $table->id();
            $table->string('name');
        });

        DB::connection('sqlite')->table('owners')->insert(['id' => 1, 'name' => 'Cliente Demo']);

        DB::connection('sqlite')->table('invoices')->insert([
            [
                'full_number' => 'POS-001',
                'owner_id' => 1,
                'user_id' => 1,
                'status' => 'issued',
                'issued_at' => Carbon::parse('2024-05-10'),
                'total' => 10000,
                'paid_total' => 5000,
                'commission_total' => 500,
            ],
            [
                'full_number' => 'POS-002',
                'owner_id' => 1,
                'user_id' => 1,
                'status' => 'paid',
                'issued_at' => Carbon::parse('2024-05-12'),
                'total' => 20000,
                'paid_total' => 20000,
                'commission_total' => 800,
            ],
        ]);

        DB::connection('sqlite')->table('invoice_payments')->insert([
            [
                'invoice_id' => 1,
                'method' => 'cash',
                'amount' => 5000,
                'paid_at' => Carbon::parse('2024-05-11'),
            ],
            [
                'invoice_id' => 2,
                'method' => 'card',
                'amount' => 20000,
                'paid_at' => Carbon::parse('2024-05-12'),
            ],
        ]);
    }

    public function test_sales_kpis(): void
    {
        $filters = new ReportFilters(Carbon::parse('2024-05-01'), Carbon::parse('2024-05-31'));
        $repo = new SalesReportRepository();
        $summary = $repo->summary($filters);

        $this->assertSame(30000.0, (float) $summary['kpis']['total_sales']);
        $this->assertSame(2, (int) $summary['kpis']['invoices_count']);
    }

    public function test_payments_kpis(): void
    {
        $filters = new ReportFilters(Carbon::parse('2024-05-01'), Carbon::parse('2024-05-31'));
        $repo = new PaymentsReportRepository();
        $summary = $repo->summary($filters);

        $this->assertSame(25000.0, (float) $summary['kpis']['total_payments']);
        $this->assertSame(2, (int) $summary['kpis']['payments_count']);
    }
}
