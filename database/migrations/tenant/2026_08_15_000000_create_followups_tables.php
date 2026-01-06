<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('followups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('code')->unique();
            $table->foreignId('patient_id')->nullable()->constrained('pacientes')->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('owners')->nullOnDelete();
            $table->foreignId('consultation_id')->nullable()->constrained('encounters')->nullOnDelete();
            $table->json('patient_snapshot')->nullable();
            $table->json('owner_snapshot')->nullable();
            $table->dateTime('followup_at');
            $table->string('performed_by')->nullable();
            $table->string('performed_by_license')->nullable();
            $table->string('reason')->nullable();
            $table->enum('improved_status', ['yes', 'no', 'partial', 'unknown'])->default('unknown');
            $table->tinyInteger('improved_score')->nullable();
            $table->longText('observations')->nullable();
            $table->longText('plan')->nullable();
            $table->dateTime('next_followup_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('followup_vitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('followup_id')->constrained('followups')->cascadeOnDelete();
            $table->decimal('temperature_c', 4, 1)->nullable();
            $table->integer('heart_rate_bpm')->nullable();
            $table->integer('respiratory_rate_rpm')->nullable();
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->enum('hydration', ['normal', 'mild_dehydration', 'moderate', 'severe', 'unknown'])->default('unknown');
            $table->enum('mucous_membranes', ['pink', 'pale', 'icteric', 'cyanotic', 'hyperemic', 'unknown'])->default('unknown');
            $table->decimal('capillary_refill_time_sec', 3, 1)->nullable();
            $table->tinyInteger('pain_score_0_10')->nullable();
            $table->integer('blood_pressure_sys')->nullable();
            $table->integer('blood_pressure_dia')->nullable();
            $table->integer('blood_pressure_map')->nullable();
            $table->integer('o2_saturation_percent')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('followup_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('followup_id')->constrained('followups')->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->string('mime');
            $table->unsignedBigInteger('size_bytes');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('followup_attachments');
        Schema::dropIfExists('followup_vitals');
        Schema::dropIfExists('followups');
    }
};
