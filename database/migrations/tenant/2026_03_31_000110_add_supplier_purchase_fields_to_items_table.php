<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (! Schema::hasColumn('items', 'estado')) {
                $table->enum('estado', ['activo', 'inactivo'])->default('activo')->after('inventariable');
            }

            if (! Schema::hasColumn('items', 'stock_minimo')) {
                $table->decimal('stock_minimo', 12, 3)->default(0)->after('stock');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'estado')) {
                $table->dropColumn('estado');
            }

            if (Schema::hasColumn('items', 'stock_minimo')) {
                $table->dropColumn('stock_minimo');
            }
        });
    }
};
