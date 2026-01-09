<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->enum('invoice_type', ['pos', 'electronic', 'credit_note', 'debit_note'])->default('pos');
            $table->string('prefix')->nullable();
            $table->unsignedBigInteger('number')->default(0);
            $table->string('full_number');
            $table->foreignId('owner_id')->constrained('owners')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['draft', 'issued', 'paid', 'void'])->default('issued');
            $table->string('currency', 10)->default('COP');
            $table->dateTime('issued_at');
            $table->text('notes')->nullable();

            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_total', 14, 2)->default(0);
            $table->decimal('tax_total', 14, 2)->default(0);
            $table->decimal('commission_total', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->decimal('paid_total', 14, 2)->default(0);
            $table->decimal('change_total', 14, 2)->default(0);

            $table->enum('electronic_status', ['not_applicable', 'pending', 'sent', 'accepted', 'rejected', 'canceled'])->default('not_applicable');
            $table->string('cufe')->nullable();
            $table->string('uuid')->nullable();
            $table->text('qr')->nullable();
            $table->json('dian_response')->nullable();
            $table->string('xml_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('accepted_at')->nullable();

            $table->timestamps();

            $table->unique(['prefix', 'number']);
            $table->index('owner_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('issued_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
