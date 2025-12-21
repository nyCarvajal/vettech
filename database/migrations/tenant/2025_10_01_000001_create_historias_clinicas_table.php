<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historias_clinicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes');
            $table->string('estado', 40)->default('borrador');
            $table->text('motivo_consulta')->nullable();
            $table->text('enfermedad_actual')->nullable();
            $table->text('antecedentes_farmacologicos')->nullable();
            $table->text('antecedentes_patologicos')->nullable();
            $table->text('antecedentes_toxicologicos')->nullable();
            $table->text('antecedentes_alergicos')->nullable();
            $table->text('antecedentes_inmunologicos')->nullable();
            $table->text('antecedentes_quirurgicos')->nullable();
            $table->text('antecedentes_ginecologicos')->nullable();
            $table->text('antecedentes_familiares')->nullable();
            $table->text('revision_sistemas')->nullable();
            $table->unsignedSmallInteger('frecuencia_cardiaca')->nullable();
            $table->string('tension_arterial', 50)->nullable();
            $table->decimal('saturacion_oxigeno', 5, 2)->nullable();
            $table->unsignedSmallInteger('frecuencia_respiratoria')->nullable();
            $table->text('examen_cabeza_cuello')->nullable();
            $table->text('examen_torax')->nullable();
            $table->text('examen_corazon')->nullable();
            $table->text('examen_mama')->nullable();
            $table->text('examen_abdomen')->nullable();
            $table->text('examen_genitales')->nullable();
            $table->text('examen_neurologico')->nullable();
            $table->text('examen_extremidades')->nullable();
            $table->text('examen_piel')->nullable();
            $table->text('analisis')->nullable();
            $table->text('plan_procedimientos')->nullable();
            $table->text('plan_medicamentos')->nullable();
            $table->string('mipres_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historias_clinicas');
    }
};
