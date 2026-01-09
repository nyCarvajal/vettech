<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_payments', function (Blueprint $table) {
            $table->enum('card_type', ['credit', 'debit'])->nullable()->after('reference');
            $table->foreignId('bank_id')->nullable()->after('card_type')->constrained('bancos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoice_payments', function (Blueprint $table) {
            $table->dropForeign(['bank_id']);
            $table->dropColumn(['card_type', 'bank_id']);
        });
    }
};
