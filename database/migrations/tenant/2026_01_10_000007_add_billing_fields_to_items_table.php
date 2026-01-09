<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->enum('type', ['product', 'service'])->default('product')->after('area');
            $table->string('sku')->nullable()->after('type');
            $table->decimal('stock', 12, 3)->default(0)->after('sku');
            $table->boolean('track_inventory')->default(true)->after('stock');
            $table->decimal('sale_price', 14, 2)->nullable()->after('track_inventory');
            $table->decimal('cost_price', 14, 2)->nullable()->after('sale_price');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['type', 'sku', 'stock', 'track_inventory', 'sale_price', 'cost_price']);
        });
    }
};
