<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('tipo_documento')->nullable()->after('id');
            $table->string('numero_documento')->nullable()->after('tipo_documento');
            $table->string('direccion')->nullable()->after('apellidos');
            $table->string('ciudad')->nullable()->after('direccion');
            $table->text('alergias')->nullable()->after('fecha_nacimiento');
            $table->text('patologias_preexistentes')->nullable()->after('alergias');
            $table->string('acompanante')->nullable()->after('patologias_preexistentes');
            $table->text('observaciones')->nullable()->after('acompanante');
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_documento',
                'numero_documento',
                'direccion',
                'ciudad',
                'alergias',
                'patologias_preexistentes',
                'acompanante',
                'observaciones',
            ]);
        });
    }
};
