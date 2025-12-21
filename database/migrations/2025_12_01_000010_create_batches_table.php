<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('batch_code');
            $table->date('expires_at');
            $table->decimal('cost', 12, 2);
            $table->integer('qty_in')->default(0);
            $table->integer('qty_out')->default(0);
            $table->integer('qty_available')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
