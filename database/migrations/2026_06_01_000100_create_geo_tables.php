<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('departamentos')) {
            Schema::create('departamentos', function (Blueprint $table) {
                $table->bigInteger('id');
                $table->string('nombre');
                $table->integer('codigo');
                $table->integer('pais_id')->nullable()->default(52);
            });
        }

        if (! Schema::hasTable('municipios')) {
            Schema::create('municipios', function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('departamento_id');
                $table->integer('codigo');
                $table->string('nombre');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('municipios');
        Schema::dropIfExists('departamentos');
    }
};
