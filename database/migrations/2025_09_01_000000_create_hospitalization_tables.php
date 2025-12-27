<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ensure hospitalization tables use the tenant connection.
     */
    protected $connection = 'tenant';

    public function up(): void
    {
        if (!Schema::hasTable('cages')) {
            Schema::create('cages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }

        Schema::create('hospital_stays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients');
            $table->foreignId('owner_id')->constrained('owners');
            $table->foreignId('cage_id')->nullable()->constrained('cages');
            $table->dateTime('admitted_at');
            $table->dateTime('discharged_at')->nullable();
            $table->enum('status', ['active', 'discharged'])->default('active');
            $table->enum('severity', ['stable', 'observation', 'critical'])->default('stable');
            $table->text('primary_dx')->nullable();
            $table->text('plan')->nullable();
            $table->text('diet')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('patient_id');
            $table->index('status');
            $table->index('admitted_at');
        });

        Schema::create('hospital_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stay_id')->constrained('hospital_stays')->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('day_number');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['stay_id', 'date']);
        });

        Schema::create('hospital_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stay_id')->constrained('hospital_stays')->cascadeOnDelete();
            $table->foreignId('day_id')->nullable()->constrained('hospital_days')->nullOnDelete();
            $table->enum('type', ['medication', 'procedure', 'feeding', 'fluid', 'other']);
            $table->enum('source', ['inventory', 'manual']);
            $table->foreignId('product_id')->nullable()->constrained('products');
            $table->string('manual_name')->nullable();
            $table->string('dose', 80)->nullable();
            $table->string('route', 50)->nullable();
            $table->string('frequency', 50)->nullable();
            $table->json('schedule_json')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->text('instructions')->nullable();
            $table->enum('status', ['active', 'stopped'])->default('active');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('stay_id');
            $table->index('type');
            $table->index('status');
        });

        Schema::create('hospital_administrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('hospital_orders')->cascadeOnDelete();
            $table->foreignId('stay_id')->constrained('hospital_stays')->cascadeOnDelete();
            $table->foreignId('day_id')->constrained('hospital_days')->cascadeOnDelete();
            $table->time('scheduled_time')->nullable();
            $table->dateTime('administered_at')->nullable();
            $table->string('dose_given', 80)->nullable();
            $table->enum('status', ['done', 'skipped', 'late'])->default('done');
            $table->text('notes')->nullable();
            $table->foreignId('administered_by')->constrained('users');
            $table->timestamps();

            $table->index('stay_id');
            $table->index('day_id');
            $table->index('administered_at');
        });

        Schema::create('hospital_vitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stay_id')->constrained('hospital_stays')->cascadeOnDelete();
            $table->foreignId('day_id')->constrained('hospital_days')->cascadeOnDelete();
            $table->dateTime('measured_at');
            $table->decimal('temp', 4, 1)->nullable();
            $table->unsignedSmallInteger('hr')->nullable();
            $table->unsignedSmallInteger('rr')->nullable();
            $table->decimal('spo2', 5, 2)->nullable();
            $table->string('bp', 30)->nullable();
            $table->decimal('weight', 6, 2)->nullable();
            $table->unsignedTinyInteger('pain_scale')->nullable();
            $table->string('hydration', 30)->nullable();
            $table->string('mucous', 30)->nullable();
            $table->string('crt', 30)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('measured_by')->constrained('users');
            $table->timestamps();

            $table->index('stay_id');
            $table->index('day_id');
            $table->index('measured_at');
        });

        Schema::create('hospital_progress_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stay_id')->constrained('hospital_stays')->cascadeOnDelete();
            $table->foreignId('day_id')->constrained('hospital_days')->cascadeOnDelete();
            $table->dateTime('logged_at');
            $table->enum('shift', ['manana', 'tarde', 'noche'])->nullable();
            $table->text('content');
            $table->foreignId('author_id')->constrained('users');
            $table->timestamps();

            $table->index('stay_id');
            $table->index('day_id');
            $table->index('logged_at');
        });

        Schema::create('hospital_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stay_id')->constrained('hospital_stays')->cascadeOnDelete();
            $table->foreignId('day_id')->nullable()->constrained('hospital_days')->nullOnDelete();
            $table->enum('source', ['service', 'inventory', 'manual']);
            $table->foreignId('product_id')->nullable()->constrained('products');
            $table->string('description');
            $table->integer('qty')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->dateTime('created_at');

            $table->index('stay_id');
            $table->index('day_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospital_charges');
        Schema::dropIfExists('hospital_progress_notes');
        Schema::dropIfExists('hospital_vitals');
        Schema::dropIfExists('hospital_administrations');
        Schema::dropIfExists('hospital_orders');
        Schema::dropIfExists('hospital_days');
        Schema::dropIfExists('hospital_stays');
        if (Schema::hasTable('cages')) {
            Schema::dropIfExists('cages');
        }
    }
};
