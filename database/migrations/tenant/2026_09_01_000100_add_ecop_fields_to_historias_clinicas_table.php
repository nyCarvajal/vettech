<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('historias_clinicas', function (Blueprint $table) {
            $table->decimal('temperatura', 4, 1)->nullable()->after('revision_sistemas');
            $table->decimal('peso', 6, 2)->nullable()->after('temperatura');
            $table->string('trc', 20)->nullable()->after('peso');
            $table->string('mucosas', 100)->nullable()->after('trc');
            $table->string('hidratacion', 100)->nullable()->after('mucosas');
            $table->string('condicion_corporal', 100)->nullable()->after('hidratacion');
            $table->text('estado_mental')->nullable()->after('frecuencia_respiratoria');
            $table->text('postura')->nullable()->after('estado_mental');
            $table->text('marcha')->nullable()->after('postura');
            $table->text('dolor')->nullable()->after('marcha');
            $table->text('examen_ojos')->nullable()->after('examen_cabeza_cuello');
            $table->text('examen_oidos')->nullable()->after('examen_ojos');
            $table->text('examen_boca')->nullable()->after('examen_oidos');
            $table->text('examen_ganglios')->nullable()->after('examen_boca');
        });
    }

    public function down(): void
    {
        Schema::table('historias_clinicas', function (Blueprint $table) {
            $table->dropColumn([
                'temperatura',
                'peso',
                'trc',
                'mucosas',
                'hidratacion',
                'condicion_corporal',
                'estado_mental',
                'postura',
                'marcha',
                'dolor',
                'examen_ojos',
                'examen_oidos',
                'examen_boca',
                'examen_ganglios',
            ]);
        });
    }
};
