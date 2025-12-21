<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('encounter_id')->nullable();
            $table->unsignedBigInteger('patient_id');
            $table->foreignId('professional_id')->constrained('usuarios')->cascadeOnDelete();
            $table->enum('status', ['draft', 'signed', 'partial', 'done'])->default('draft');
            $table->timestamps();

            $table->index('patient_id');
        });

        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('dose');
            $table->string('frequency');
            $table->unsignedInteger('duration_days');
            $table->text('instructions')->nullable();
            $table->unsignedInteger('qty_requested');
            $table->timestamps();
        });

        Schema::create('dispensations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dispensed_by')->constrained('usuarios')->cascadeOnDelete();
            $table->dateTime('dispensed_at');
            $table->enum('status', ['partial', 'done'])->default('partial');
            $table->timestamps();
        });

        Schema::create('dispensation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispensation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
            $table->unsignedInteger('qty_dispensed');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('cost_snapshot', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispensation_items');
        Schema::dropIfExists('dispensations');
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
    }
};
