<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_owner', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('owners')->cascadeOnDelete();
            $table->string('relationship')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['patient_id', 'owner_id']);
            $table->index(['patient_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_owner');
    }
};
