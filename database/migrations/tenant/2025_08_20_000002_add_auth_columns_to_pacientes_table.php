<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            if (! Schema::hasColumn('pacientes', 'password')) {
                $table->string('password')->nullable()->after('correo');
            }

            if (! Schema::hasColumn('pacientes', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('password');
            }

            if (! Schema::hasColumn('pacientes', 'verification_token')) {
                $table->string('verification_token', 80)->nullable()->after('email_verified_at');
            }

            if (! Schema::hasColumn('pacientes', 'remember_token')) {
                $table->rememberToken()->after('verification_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            if (Schema::hasColumn('pacientes', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
            if (Schema::hasColumn('pacientes', 'verification_token')) {
                $table->dropColumn('verification_token');
            }
            if (Schema::hasColumn('pacientes', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            if (Schema::hasColumn('pacientes', 'password')) {
                $table->dropColumn('password');
            }
        });
    }
};
