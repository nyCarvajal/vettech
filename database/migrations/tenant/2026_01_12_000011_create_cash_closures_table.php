<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_closures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinica_id')->nullable();
            $table->date('date');
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['open', 'closed'])->default('closed');
            $table->decimal('expected_cash', 14, 2)->default(0);
            $table->decimal('counted_cash', 14, 2)->default(0);
            $table->decimal('difference', 14, 2)->default(0);
            $table->decimal('expected_card', 14, 2)->nullable();
            $table->decimal('counted_card', 14, 2)->nullable();
            $table->decimal('expected_transfer', 14, 2)->nullable();
            $table->decimal('counted_transfer', 14, 2)->nullable();
            $table->decimal('total_expected', 14, 2)->default(0);
            $table->decimal('total_counted', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['clinica_id', 'date']);
            $table->index('user_id');
            $table->index(['date', 'clinica_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_closures');
    }
};
