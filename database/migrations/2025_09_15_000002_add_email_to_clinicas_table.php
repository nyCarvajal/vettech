<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clinicas', function (Blueprint $table) {
            if (! Schema::hasColumn('clinicas', 'email')) {
                $table->string('email', 191)->nullable()->after('direccion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinicas', function (Blueprint $table) {
            if (Schema::hasColumn('clinicas', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};
