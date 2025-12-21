<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['med', 'insumo', 'alimento', 'servicio']);
            $table->string('sku')->nullable();
            $table->string('unit');
            $table->boolean('requires_batch')->default(false);
            $table->unsignedInteger('min_stock')->default(0);
            $table->decimal('sale_price', 12, 2);
            $table->decimal('cost_avg', 12, 2)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('type');
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
