<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('acompanante_contacto', 50)
                ->nullable()
                ->after('acompanante');
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn('acompanante_contacto');
        });
    }
};
