<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE hospital_vitals MODIFY pain_scale VARCHAR(30) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE hospital_vitals MODIFY pain_scale TINYINT UNSIGNED NULL");
    }
};
