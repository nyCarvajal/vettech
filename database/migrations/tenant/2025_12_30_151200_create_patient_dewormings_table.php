<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patient_dewormings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('consulta_id')->nullable()->constrained('historia_clinicas');
            $table->enum('type', ['internal', 'external']);
            $table->date('applied_at')->default(DB::raw('CURRENT_DATE'));
            $table->foreignId('item_id')->nullable()->constrained('items');
            $table->string('item_manual')->nullable();
            $table->string('dose')->nullable();
            $table->string('route')->nullable();
            $table->integer('duration_days')->nullable();
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
        Schema::dropIfExists('patient_dewormings');
    }
};
