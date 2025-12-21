<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('batches')->cascadeOnDelete();
            $table->enum('type', ['in', 'out', 'adjust', 'merma', 'return']);
            $table->integer('qty');
            $table->string('reason', 255);
            $table->string('ref_entity', 50)->nullable();
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->foreignId('user_id')->constrained('usuarios')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['product_id', 'batch_id']);
            $table->index(['ref_entity', 'ref_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
