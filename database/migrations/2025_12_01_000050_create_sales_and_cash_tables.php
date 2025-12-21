<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreignId('created_by')->constrained('usuarios')->cascadeOnDelete();
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('status', ['open', 'paid', 'void'])->default('open');
            $table->timestamps();

            $table->index(['owner_id', 'patient_id']);
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('qty');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->string('ref_entity')->nullable();
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->timestamps();
        });

        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->timestamps();
        });

        Schema::create('cash_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_register_id')->constrained()->cascadeOnDelete();
            $table->foreignId('opened_by')->constrained('usuarios')->cascadeOnDelete();
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->decimal('opening_amount', 12, 2);
            $table->decimal('closing_amount_expected', 12, 2)->nullable();
            $table->decimal('closing_amount_counted', 12, 2)->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_session_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount', 12, 2);
            $table->enum('method', ['cash', 'card', 'transfer']);
            $table->string('reason');
            $table->string('ref_entity')->nullable();
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->foreignId('created_by')->constrained('usuarios')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_movements');
        Schema::dropIfExists('cash_sessions');
        Schema::dropIfExists('cash_registers');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
    }
};
