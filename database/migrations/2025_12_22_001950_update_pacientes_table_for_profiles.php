<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->foreignId('owner_id')->nullable()->after('id')->constrained('owners')->nullOnDelete();
            $table->foreignId('species_id')->nullable()->after('owner_id')->constrained('species')->nullOnDelete();
            $table->foreignId('breed_id')->nullable()->after('species_id')->constrained('breeds')->nullOnDelete();
            $table->enum('sexo', ['M', 'F', 'NA'])->nullable()->after('ciudad');
            $table->string('color')->nullable()->after('fecha_nacimiento');
            $table->string('microchip')->nullable()->after('color');
            $table->decimal('peso_actual', 6, 2)->nullable()->after('microchip');
            $table->enum('temperamento', ['tranquilo', 'nervioso', 'agresivo', 'miedoso', 'otro'])->nullable()->after('peso_actual');
            $table->text('alergias')->nullable()->after('temperamento');
            $table->string('photo_path')->nullable()->after('alergias');
            $table->text('observaciones')->nullable();

            $table->index('owner_id');
            $table->index('species_id');
            $table->index('breed_id');
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('owner_id');
            $table->dropConstrainedForeignId('species_id');
            $table->dropConstrainedForeignId('breed_id');
            $table->dropColumn(['sexo', 'color', 'microchip', 'peso_actual', 'temperamento', 'photo_path']);
            $table->date('fecha_nacimiento')->nullable()->change();
            $table->text('alergias')->nullable()->change();
            $table->text('observaciones')->nullable()->change();
        });
    }
};
