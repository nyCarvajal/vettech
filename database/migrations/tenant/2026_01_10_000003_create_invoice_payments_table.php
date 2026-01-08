<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->enum('method', ['cash', 'card', 'transfer', 'mixed']);
            $table->decimal('amount', 14, 2);
            $table->decimal('received', 14, 2)->nullable();
            $table->decimal('change', 14, 2)->nullable();
            $table->string('reference')->nullable();
            $table->dateTime('paid_at');
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
    }
};
