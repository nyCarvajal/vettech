<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->enum('movement_type', ['sale', 'sale_void', 'adjustment']);
            $table->decimal('quantity', 12, 3);
            $table->decimal('unit_cost', 14, 2)->nullable();
            $table->string('related_type');
            $table->unsignedBigInteger('related_id');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('occurred_at');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('item_id');
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
