<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos');
            $table->foreignId('municipio_id')->nullable()->constrained('municipios');
        });
    }

    public function down(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->dropConstrainedForeignId('departamento_id');
            $table->dropConstrainedForeignId('municipio_id');
        });
    }
};
