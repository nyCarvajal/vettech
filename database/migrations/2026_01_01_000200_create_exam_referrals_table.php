<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('exam_referrals')) {
            Schema::create('exam_referrals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('historia_clinica_id')->nullable()->constrained('historias_clinicas');
                $table->foreignId('patient_id')->constrained('pacientes');
                $table->string('doctor_name')->nullable();
                $table->text('tests')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_referrals');
    }
};
