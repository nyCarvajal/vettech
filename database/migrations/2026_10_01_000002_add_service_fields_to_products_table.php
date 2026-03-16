<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'estimated_duration_minutes')) {
                $table->unsignedInteger('estimated_duration_minutes')->nullable()->after('cost_avg');
            }
            if (! Schema::hasColumn('products', 'authorized_roles')) {
                $table->json('authorized_roles')->nullable()->after('estimated_duration_minutes');
            }
            if (! Schema::hasColumn('products', 'cost_structure')) {
                $table->json('cost_structure')->nullable()->after('authorized_roles');
            }
            if (! Schema::hasColumn('products', 'cost_structure_commission_percent')) {
                $table->decimal('cost_structure_commission_percent', 5, 2)->nullable()->after('cost_structure');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            foreach (['estimated_duration_minutes', 'authorized_roles', 'cost_structure', 'cost_structure_commission_percent'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
