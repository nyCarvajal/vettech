<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encounters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('pacientes')->cascadeOnDelete();
            $table->dateTime('occurred_at');
            $table->string('professional')->nullable();
            $table->string('motivo')->nullable();
            $table->string('diagnostico')->nullable();
            $table->text('plan')->nullable();
            $table->decimal('peso', 6, 2)->nullable();
            $table->decimal('temperatura', 4, 1)->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encounters');
    }
};
