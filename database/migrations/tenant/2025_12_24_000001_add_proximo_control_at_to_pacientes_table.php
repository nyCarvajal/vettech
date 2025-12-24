<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            if (! Schema::hasColumn('pacientes', 'proximo_control_at')) {
                $table->dateTime('proximo_control_at')->nullable()->after('fecha_nacimiento');
                $table->index('proximo_control_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            if (Schema::hasColumn('pacientes', 'proximo_control_at')) {
                $table->dropIndex(['proximo_control_at']);
                $table->dropColumn('proximo_control_at');
            }
        });
    }
};
