<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patient_immunizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('consulta_id')->nullable()->constrained('historia_clinicas');
            $table->date('applied_at')->default(DB::raw('CURRENT_DATE'));
            $table->string('vaccine_name');
            $table->boolean('contains_rabies')->default(false);
            $table->foreignId('item_id')->nullable()->constrained('items');
            $table->string('item_manual')->nullable();
            $table->string('batch_lot');
            $table->string('dose')->nullable();
            $table->date('next_due_at')->nullable();
            $table->foreignId('vet_user_id')->nullable()->constrained('usuarios');
            $table->text('notes')->nullable();
            $table->enum('status', ['applied', 'scheduled', 'overdue'])->default('applied');
            $table->timestamps();

            $table->index(['paciente_id', 'applied_at']);
            $table->index('next_due_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_immunizations');
    }
};
