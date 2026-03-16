<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (! Schema::hasColumn('items', 'estimated_duration_minutes')) {
                $table->unsignedInteger('estimated_duration_minutes')->nullable()->after('inventariable');
            }

            if (! Schema::hasColumn('items', 'authorized_roles')) {
                $table->json('authorized_roles')->nullable()->after('estimated_duration_minutes');
            }

            if (! Schema::hasColumn('items', 'cost_structure')) {
                $table->json('cost_structure')->nullable()->after('authorized_roles');
            }

            if (! Schema::hasColumn('items', 'cost_structure_commission_percent')) {
                $table->decimal('cost_structure_commission_percent', 5, 2)->nullable()->after('cost_structure');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $columns = [
                'estimated_duration_minutes',
                'authorized_roles',
                'cost_structure',
                'cost_structure_commission_percent',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
