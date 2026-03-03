<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::table('hospital_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('hospital_orders', 'frequency_type')) {
                $table->string('frequency_type', 30)->nullable()->after('frequency');
            }
            if (! Schema::hasColumn('hospital_orders', 'frequency_value')) {
                $table->unsignedInteger('frequency_value')->nullable()->after('frequency_type');
            }
            if (! Schema::hasColumn('hospital_orders', 'duration_days')) {
                $table->unsignedInteger('duration_days')->nullable()->after('end_at');
            }
            if (! Schema::hasColumn('hospital_orders', 'next_due_at')) {
                $table->dateTime('next_due_at')->nullable()->after('duration_days');
            }
            if (! Schema::hasColumn('hospital_orders', 'last_applied_at')) {
                $table->dateTime('last_applied_at')->nullable()->after('next_due_at');
            }
        });

        Schema::table('hospital_charges', function (Blueprint $table) {
            if (! Schema::hasColumn('hospital_charges', 'patient_id')) {
                $table->foreignId('patient_id')->nullable()->after('stay_id')->constrained('patients')->nullOnDelete();
            }
            if (! Schema::hasColumn('hospital_charges', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('day_id')->constrained('hospital_orders')->nullOnDelete();
            }
            if (! Schema::hasColumn('hospital_charges', 'application_id')) {
                $table->foreignId('application_id')->nullable()->after('order_id')->constrained('hospital_administrations')->nullOnDelete();
            }
            if (! Schema::hasColumn('hospital_charges', 'ref_type')) {
                $table->string('ref_type', 60)->nullable()->after('application_id');
            }
            if (! Schema::hasColumn('hospital_charges', 'ref_id')) {
                $table->unsignedBigInteger('ref_id')->nullable()->after('ref_type');
            }
            if (! Schema::hasColumn('hospital_charges', 'status')) {
                $table->string('status', 20)->default('pending')->after('total');
            }
        });

        DB::table('hospital_orders')
            ->whereNull('frequency_type')
            ->update([
                'frequency_type' => 'q_hours',
                'frequency_value' => 8,
            ]);

        DB::table('hospital_orders')
            ->whereNull('next_due_at')
            ->update([
                'next_due_at' => DB::raw('COALESCE(start_at, created_at)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('hospital_charges', function (Blueprint $table) {
            foreach (['patient_id', 'order_id', 'application_id'] as $fk) {
                try {
                    $table->dropForeign([$fk]);
                } catch (Throwable $e) {
                    // noop
                }
            }
            foreach (['patient_id', 'order_id', 'application_id', 'ref_type', 'ref_id', 'status'] as $column) {
                if (Schema::hasColumn('hospital_charges', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('hospital_orders', function (Blueprint $table) {
            foreach (['frequency_type', 'frequency_value', 'duration_days', 'next_due_at', 'last_applied_at'] as $column) {
                if (Schema::hasColumn('hospital_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
