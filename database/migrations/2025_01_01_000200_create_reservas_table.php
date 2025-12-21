<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha');
            $table->unsignedInteger('duracion')->default(60);
            $table->unsignedBigInteger('paciente_id')->nullable();
            $table->unsignedBigInteger('entrenador_id')->nullable();
            $table->unsignedBigInteger('tipocita_id')->nullable();
            $table->string('estado')->default('Pendiente');
            $table->string('tipo')->default('Reserva');
            $table->string('modalidad')->default('Presencial');
            $table->string('visita_tipo')->default('Control');
            $table->text('nota_cliente')->nullable();
            $table->timestamps();

            $table->foreign('paciente_id')->references('id')->on('pacientes')->nullOnDelete();
            $table->foreign('entrenador_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('tipocita_id')->references('id')->on('tipocitas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
