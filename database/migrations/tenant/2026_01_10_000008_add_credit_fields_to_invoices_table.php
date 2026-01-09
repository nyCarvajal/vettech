<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('is_credit')->default(false)->after('notes');
            $table->unsignedSmallInteger('credit_days')->nullable()->after('is_credit');
            $table->dateTime('due_at')->nullable()->after('credit_days');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['is_credit', 'credit_days', 'due_at']);
        });
    }
};
