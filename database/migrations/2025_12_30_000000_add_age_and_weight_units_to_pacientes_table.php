<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            if (! Schema::hasColumn('pacientes', 'age_value')) {
                $table->unsignedInteger('age_value')->nullable()->after('fecha_nacimiento');
            }

            if (! Schema::hasColumn('pacientes', 'age_unit')) {
                $table->enum('age_unit', ['years', 'months'])->nullable()->after('age_value');
            }

            if (! Schema::hasColumn('pacientes', 'weight_unit')) {
                $table->enum('weight_unit', ['kg', 'g'])->default('kg')->after('peso_actual');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            if (Schema::hasColumn('pacientes', 'weight_unit')) {
                $table->dropColumn('weight_unit');
            }

            if (Schema::hasColumn('pacientes', 'age_unit')) {
                $table->dropColumn('age_unit');
            }

            if (Schema::hasColumn('pacientes', 'age_value')) {
                $table->dropColumn('age_value');
            }
        });
    }
};
