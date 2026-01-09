<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $connection = DB::connection('tenant');

        if ($connection->getDriverName() === 'mysql') {
            $connection->statement(
                "ALTER TABLE inventory_movements MODIFY movement_type ENUM('sale','sale_void','adjustment','entry','exit','adjust','initial')"
            );
        }
    }

    public function down(): void
    {
        $connection = DB::connection('tenant');

        if ($connection->getDriverName() === 'mysql') {
            $connection->statement(
                "ALTER TABLE inventory_movements MODIFY movement_type ENUM('sale','sale_void','adjustment')"
            );
        }
    }
};
