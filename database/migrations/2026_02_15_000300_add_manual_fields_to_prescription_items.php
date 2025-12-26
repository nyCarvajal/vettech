<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        DB::statement('ALTER TABLE prescription_items MODIFY product_id BIGINT UNSIGNED NULL');

        Schema::table('prescription_items', function (Blueprint $table) {
            $table->string('manual_name')->nullable()->after('product_id');
            $table->boolean('is_manual')->default(false)->after('manual_name');
            $table->boolean('billable')->default(true)->after('is_manual');
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['manual_name', 'is_manual', 'billable']);
        });

        DB::statement('ALTER TABLE prescription_items MODIFY product_id BIGINT UNSIGNED NOT NULL');

        Schema::table('prescription_items', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }
};
