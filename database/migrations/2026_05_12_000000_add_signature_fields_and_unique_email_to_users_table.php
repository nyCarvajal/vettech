<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'firma_medica_texto')) {
                $table->string('firma_medica_texto')->nullable();
            }

            if (! Schema::hasColumn('users', 'firma_medica_url')) {
                $table->string('firma_medica_url', 500)->nullable();
            }

            if (! Schema::hasColumn('users', 'firma_medica_public_id')) {
                $table->string('firma_medica_public_id')->nullable();
            }
        });

        if (! $this->hasEmailUniqueIndex()) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if ($this->hasEmailUniqueIndex()) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_email_unique');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'firma_medica_public_id')) {
                $table->dropColumn('firma_medica_public_id');
            }

            if (Schema::hasColumn('users', 'firma_medica_url')) {
                $table->dropColumn('firma_medica_url');
            }

            if (Schema::hasColumn('users', 'firma_medica_texto')) {
                $table->dropColumn('firma_medica_texto');
            }
        });
    }

    private function hasEmailUniqueIndex(): bool
    {
        $indexes = DB::select("SHOW INDEX FROM users WHERE Column_name = 'email'");

        foreach ($indexes as $index) {
            if (isset($index->Non_unique) && (int) $index->Non_unique === 0) {
                return true;
            }
        }

        return false;
    }
};
