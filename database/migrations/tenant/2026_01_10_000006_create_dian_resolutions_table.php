<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dian_resolutions', function (Blueprint $table) {
            $table->id();
            $table->string('prefix')->nullable();
            $table->unsignedBigInteger('range_start');
            $table->unsignedBigInteger('range_end');
            $table->unsignedBigInteger('current_number')->default(0);
            $table->date('valid_from');
            $table->date('valid_until');
            $table->string('resolution_number');
            $table->string('technical_key')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dian_resolutions');
    }
};
