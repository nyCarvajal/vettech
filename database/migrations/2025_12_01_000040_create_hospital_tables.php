<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('hospital_stays', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->foreignId('cage_id')->constrained()->cascadeOnDelete();
            $table->dateTime('admitted_at');
            $table->dateTime('discharged_at')->nullable();
            $table->enum('status', ['active', 'discharged'])->default('active');
            $table->enum('severity', ['stable', 'observation', 'critical'])->default('observation');
            $table->text('diagnosis');
            $table->text('plan');
            $table->text('diet')->nullable();
            $table->foreignId('created_by')->constrained('usuarios')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('shift_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('shift_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_definition_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->timestamps();

            $table->unique(['shift_definition_id', 'date']);
        });

        Schema::create('hospital_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stay_id')->constrained('hospital_stays')->cascadeOnDelete();
            $table->enum('category', ['med', 'fluidos', 'alimento', 'control', 'procedimiento']);
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->json('times_json');
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->foreignId('created_by')->constrained('usuarios')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('hospital_task_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('hospital_tasks')->cascadeOnDelete();
            $table->foreignId('shift_instance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('performed_by')->constrained('usuarios')->cascadeOnDelete();
            $table->dateTime('performed_at')->nullable();
            $table->enum('status', ['done', 'skipped']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('hospital_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stay_id')->constrained('hospital_stays')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('batches')->cascadeOnDelete();
            $table->integer('qty');
            $table->enum('source', ['task', 'manual']);
            $table->foreignId('created_by')->constrained('usuarios')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['stay_id', 'product_id']);
        });

        Schema::create('handoff_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stay_id')->constrained('hospital_stays')->cascadeOnDelete();
            $table->foreignId('shift_instance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('usuarios')->cascadeOnDelete();
            $table->text('summary');
            $table->text('pending')->nullable();
            $table->text('alerts')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('handoff_notes');
        Schema::dropIfExists('hospital_consumptions');
        Schema::dropIfExists('hospital_task_logs');
        Schema::dropIfExists('hospital_tasks');
        Schema::dropIfExists('shift_instances');
        Schema::dropIfExists('shift_definitions');
        Schema::dropIfExists('hospital_stays');
        Schema::dropIfExists('cages');
    }
};
