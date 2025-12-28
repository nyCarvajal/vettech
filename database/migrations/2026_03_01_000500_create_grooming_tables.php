<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('grooming_services')) {
            Schema::create('grooming_services', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedInteger('duration_minutes')->nullable();
                $table->decimal('default_price', 12, 2)->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }

        Schema::create('groomings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('patient_id')->constrained('pacientes');
            $table->foreignId('owner_id')->constrained('owners');
            $table->dateTime('scheduled_at');
            $table->enum('status', ['agendado', 'en_proceso', 'finalizado', 'cancelado'])->default('agendado');
            $table->boolean('needs_pickup')->default(false);
            $table->string('pickup_address')->nullable();
            $table->boolean('external_deworming')->default(false);
            $table->enum('deworming_source', ['none', 'manual', 'inventory'])->default('none');
            $table->foreignId('deworming_product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('deworming_product_name')->nullable();
            $table->text('indications')->nullable();
            $table->enum('service_source', ['none', 'product', 'grooming_service'])->default('none');
            $table->foreignId('service_id')->nullable()->constrained('grooming_services')->nullOnDelete();
            $table->foreignId('product_service_id')->nullable()->constrained('products')->nullOnDelete();
            $table->decimal('service_price', 12, 2)->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->timestamps();

            $table->index('patient_id');
            $table->index('owner_id');
            $table->index('scheduled_at');
            $table->index('status');
        });

        Schema::create('grooming_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grooming_id')->unique()->constrained('groomings')->cascadeOnDelete();
            $table->boolean('fleas')->default(false);
            $table->boolean('ticks')->default(false);
            $table->boolean('skin_issue')->default(false);
            $table->boolean('ear_issue')->default(false);
            $table->text('observations')->nullable();
            $table->text('recommendations')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('grooming_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grooming_id')->constrained('groomings')->cascadeOnDelete();
            $table->enum('type', ['before', 'after']);
            $table->string('file_path');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grooming_media');
        Schema::dropIfExists('grooming_reports');
        Schema::dropIfExists('groomings');
        Schema::dropIfExists('grooming_services');
    }
};
