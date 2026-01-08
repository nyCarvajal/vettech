<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_settings', function (Blueprint $table) {
            $table->id();
            $table->string('pos_prefix')->default('POS');
            $table->decimal('default_tax_rate', 6, 3)->default(0);
            $table->decimal('default_commission_rate', 6, 3)->default(0);
            $table->string('currency', 10)->default('COP');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_settings');
    }
};
