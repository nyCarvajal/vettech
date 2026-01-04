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
        Schema::create('procedures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('code')->unique();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->json('patient_snapshot');
            $table->json('owner_snapshot')->nullable();
            $table->enum('type', ['surgery', 'procedure']);
            $table->string('name');
            $table->string('category')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'canceled'])->default('scheduled');
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->string('location')->nullable();
            $table->string('responsible_vet_name')->nullable();
            $table->string('responsible_vet_license')->nullable();
            $table->json('assistants')->nullable();
            $table->longText('preop_notes')->nullable();
            $table->longText('intraop_notes')->nullable();
            $table->longText('postop_notes')->nullable();
            $table->longText('observations')->nullable();
            $table->longText('anesthesia_plan')->nullable();
            $table->longText('anesthesia_notes')->nullable();
            $table->longText('anesthesia_monitoring')->nullable();
            $table->longText('pain_management')->nullable();
            $table->longText('complications')->nullable();
            $table->string('diagnosis_pre')->nullable();
            $table->string('diagnosis_post')->nullable();
            $table->longText('lab_results_summary')->nullable();
            $table->unsignedBigInteger('consent_document_id')->nullable();
            $table->decimal('cost_total', 12, 2)->nullable();
            $table->string('currency')->default('COP');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'patient_id', 'owner_id']);
        });

        Schema::create('procedure_anesthesia_medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procedure_id')->constrained()->cascadeOnDelete();
            $table->string('drug_name');
            $table->string('dose')->nullable();
            $table->string('dose_unit')->nullable();
            $table->string('route')->nullable();
            $table->string('frequency')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('procedure_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procedure_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->string('mime');
            $table->unsignedBigInteger('size_bytes');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
        });

        Schema::create('procedure_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procedure_id')->constrained()->cascadeOnDelete();
            $table->string('event_type');
            $table->json('payload')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedure_events');
        Schema::dropIfExists('procedure_attachments');
        Schema::dropIfExists('procedure_anesthesia_medications');
        Schema::dropIfExists('procedures');
    }
};
