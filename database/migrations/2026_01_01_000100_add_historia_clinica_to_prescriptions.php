<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('prescriptions', 'historia_clinica_id')) {
                $table->foreignId('historia_clinica_id')->nullable()->after('encounter_id')->constrained('historias_clinicas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            if (Schema::hasColumn('prescriptions', 'historia_clinica_id')) {
                $table->dropConstrainedForeignId('historia_clinica_id');
            }
        });
    }
};
