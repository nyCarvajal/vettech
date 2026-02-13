<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            if (! Schema::hasColumn('owners', 'password')) {
                $table->string('password')->nullable()->after('email');
            }

            if (! Schema::hasColumn('owners', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('password');
            }

            if (! Schema::hasColumn('owners', 'verification_token')) {
                $table->string('verification_token', 64)->nullable()->after('email_verified_at');
            }

            if (! Schema::hasColumn('owners', 'remember_token')) {
                $table->rememberToken();
            }
        });
    }

    public function down(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            foreach (['remember_token', 'verification_token', 'email_verified_at', 'password'] as $column) {
                if (Schema::hasColumn('owners', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
