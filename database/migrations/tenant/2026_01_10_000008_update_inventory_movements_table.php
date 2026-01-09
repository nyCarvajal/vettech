<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            if (! Schema::hasColumn('inventory_movements', 'before_stock')) {
                $table->decimal('before_stock', 12, 3)->nullable()->after('quantity');
            }

            if (! Schema::hasColumn('inventory_movements', 'after_stock')) {
                $table->decimal('after_stock', 12, 3)->nullable()->after('before_stock');
            }

            if (! Schema::hasColumn('inventory_movements', 'reference')) {
                $table->string('reference')->nullable()->after('after_stock');
            }

            if (! Schema::hasColumn('inventory_movements', 'notes')) {
                $table->text('notes')->nullable()->after('reference');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE inventory_movements MODIFY movement_type ENUM('sale','sale_void','adjustment','entry','exit','adjust','initial')"
            );

            DB::statement('ALTER TABLE inventory_movements MODIFY related_type VARCHAR(255) NULL');
            DB::statement('ALTER TABLE inventory_movements MODIFY related_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE inventory_movements MODIFY user_id BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE inventory_movements MODIFY movement_type ENUM('sale','sale_void','adjustment')");
            DB::statement('ALTER TABLE inventory_movements MODIFY related_type VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE inventory_movements MODIFY related_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE inventory_movements MODIFY user_id BIGINT UNSIGNED NOT NULL');
        }

        Schema::table('inventory_movements', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_movements', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('inventory_movements', 'reference')) {
                $table->dropColumn('reference');
            }
            if (Schema::hasColumn('inventory_movements', 'after_stock')) {
                $table->dropColumn('after_stock');
            }
            if (Schema::hasColumn('inventory_movements', 'before_stock')) {
                $table->dropColumn('before_stock');
            }
        });
    }
};
