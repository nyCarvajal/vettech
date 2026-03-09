<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('historias_clinicas', function (Blueprint $table) {
            $table->text('abordaje_lista_problemas')->nullable()->after('analisis');
            $table->text('abordaje_lista_maestra')->nullable()->after('abordaje_lista_problemas');
            $table->text('abordaje_diagnosticos_diferenciales')->nullable()->after('abordaje_lista_maestra');
            $table->text('diagnostico_definitivo')->nullable()->after('abordaje_diagnosticos_diferenciales');
        });
    }

    public function down(): void
    {
        Schema::table('historias_clinicas', function (Blueprint $table) {
            $table->dropColumn([
                'abordaje_lista_problemas',
                'abordaje_lista_maestra',
                'abordaje_diagnosticos_diferenciales',
                'diagnostico_definitivo',
            ]);
        });
    }
};
