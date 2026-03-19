<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groomings', function (Blueprint $table) {
            if (! Schema::hasColumn('groomings', 'service_item_id')) {
                $table->unsignedBigInteger('service_item_id')->nullable()->after('product_service_id');
            }

            if (! Schema::hasColumn('groomings', 'service_item_name')) {
                $table->string('service_item_name')->nullable()->after('service_item_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('groomings', function (Blueprint $table) {
            if (Schema::hasColumn('groomings', 'service_item_name')) {
                $table->dropColumn('service_item_name');
            }

            if (Schema::hasColumn('groomings', 'service_item_id')) {
                $table->dropColumn('service_item_id');
            }
        });
    }
};
