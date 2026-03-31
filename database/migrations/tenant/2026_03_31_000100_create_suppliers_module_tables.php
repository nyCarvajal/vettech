<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social', 200);
            $table->string('tipo_documento', 30)->nullable();
            $table->string('numero_documento', 50)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('celular', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->string('ciudad', 120)->nullable();
            $table->string('contacto_principal', 150)->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('razon_social');
            $table->index('numero_documento');
            $table->index('telefono');
        });

        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->string('numero_factura', 60);
            $table->date('fecha_factura');
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('descuento', 14, 2)->default(0);
            $table->decimal('impuestos', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->decimal('total_pagado', 14, 2)->default(0);
            $table->decimal('saldo_pendiente', 14, 2)->default(0);
            $table->enum('estado_pago', ['pendiente', 'parcial', 'pagado', 'vencido'])->default('pendiente');
            $table->enum('estado', ['borrador', 'confirmada', 'anulada'])->default('borrador');
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['supplier_id', 'numero_factura']);
            $table->index(['estado', 'estado_pago']);
            $table->index('fecha_vencimiento');
        });

        Schema::create('supplier_invoice_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_invoice_id')->constrained('supplier_invoices')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items');
            $table->string('descripcion', 250)->nullable();
            $table->decimal('cantidad', 12, 3);
            $table->decimal('costo_unitario', 14, 2);
            $table->decimal('precio_venta_unitario', 14, 2)->nullable();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->boolean('es_obsequio')->default(false);
            $table->boolean('afecta_valor')->default(true);
            $table->timestamps();
        });

        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('supplier_invoice_id')->nullable()->constrained('supplier_invoices')->nullOnDelete();
            $table->date('fecha_pago');
            $table->decimal('valor', 14, 2);
            $table->string('metodo_pago', 40)->nullable();
            $table->enum('origen_fondos', ['caja_menor', 'banco']);
            $table->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
            $table->foreignId('banco_id')->nullable()->constrained('bancos')->nullOnDelete();
            $table->string('referencia', 80)->nullable();
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['supplier_id', 'fecha_pago']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
        Schema::dropIfExists('supplier_invoice_details');
        Schema::dropIfExists('supplier_invoices');
        Schema::dropIfExists('suppliers');
    }
};
