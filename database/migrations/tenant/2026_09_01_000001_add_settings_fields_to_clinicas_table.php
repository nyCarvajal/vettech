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
            if (! Schema::hasColumn('clinicas', 'name')) {
                $table->string('name', 300)->nullable()->after('nombre');
            }
            if (! Schema::hasColumn('clinicas', 'nit')) {
                $table->string('nit', 100)->nullable()->after('name');
            }
            if (! Schema::hasColumn('clinicas', 'dv')) {
                $table->string('dv', 2)->nullable()->after('nit');
            }
            if (! Schema::hasColumn('clinicas', 'regimen')) {
                $table->string('regimen', 191)->nullable()->after('dv');
            }
            if (! Schema::hasColumn('clinicas', 'responsable_iva')) {
                $table->boolean('responsable_iva')->default(false)->after('regimen');
            }
            if (! Schema::hasColumn('clinicas', 'email')) {
                $table->string('email', 191)->nullable()->after('responsable_iva');
            }
            if (! Schema::hasColumn('clinicas', 'phone')) {
                $table->string('phone', 50)->nullable()->after('email');
            }
            if (! Schema::hasColumn('clinicas', 'address')) {
                $table->string('address', 300)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('clinicas', 'city')) {
                $table->string('city', 191)->nullable()->after('address');
            }
            if (! Schema::hasColumn('clinicas', 'department')) {
                $table->string('department', 191)->nullable()->after('city');
            }
            if (! Schema::hasColumn('clinicas', 'country')) {
                $table->string('country', 2)->default('CO')->after('department');
            }
            if (! Schema::hasColumn('clinicas', 'website')) {
                $table->string('website', 191)->nullable()->after('country');
            }
            if (! Schema::hasColumn('clinicas', 'logo_path')) {
                $table->string('logo_path', 255)->nullable()->after('website');
            }
            if (! Schema::hasColumn('clinicas', 'logo_dark_path')) {
                $table->string('logo_dark_path', 255)->nullable()->after('logo_path');
            }
            if (! Schema::hasColumn('clinicas', 'primary_color')) {
                $table->string('primary_color', 20)->nullable()->after('logo_dark_path');
            }
            if (! Schema::hasColumn('clinicas', 'secondary_color')) {
                $table->string('secondary_color', 20)->nullable()->after('primary_color');
            }
            if (! Schema::hasColumn('clinicas', 'footer_note')) {
                $table->text('footer_note')->nullable()->after('secondary_color');
            }
            if (! Schema::hasColumn('clinicas', 'header_note')) {
                $table->text('header_note')->nullable()->after('footer_note');
            }
            if (! Schema::hasColumn('clinicas', 'payment_terms')) {
                $table->text('payment_terms')->nullable()->after('header_note');
            }
            if (! Schema::hasColumn('clinicas', 'payment_due_days')) {
                $table->integer('payment_due_days')->nullable()->after('payment_terms');
            }
            if (! Schema::hasColumn('clinicas', 'invoice_prefix')) {
                $table->string('invoice_prefix', 20)->nullable()->after('payment_due_days');
            }
            if (! Schema::hasColumn('clinicas', 'invoice_footer_legal')) {
                $table->text('invoice_footer_legal')->nullable()->after('invoice_prefix');
            }
            if (! Schema::hasColumn('clinicas', 'default_tax_rate')) {
                $table->decimal('default_tax_rate', 6, 3)->default(0)->after('invoice_footer_legal');
            }
            if (! Schema::hasColumn('clinicas', 'bank_account_info')) {
                $table->text('bank_account_info')->nullable()->after('default_tax_rate');
            }
            if (! Schema::hasColumn('clinicas', 'whatsapp_number')) {
                $table->string('whatsapp_number', 50)->nullable()->after('bank_account_info');
            }
            if (! Schema::hasColumn('clinicas', 'dian_enabled')) {
                $table->boolean('dian_enabled')->default(false)->after('whatsapp_number');
            }
            if (! Schema::hasColumn('clinicas', 'dian_software_id')) {
                $table->string('dian_software_id', 191)->nullable()->after('dian_enabled');
            }
            if (! Schema::hasColumn('clinicas', 'dian_software_pin')) {
                $table->string('dian_software_pin', 50)->nullable()->after('dian_software_id');
            }
            if (! Schema::hasColumn('clinicas', 'dian_test_set_id')) {
                $table->string('dian_test_set_id', 191)->nullable()->after('dian_software_pin');
            }
            if (! Schema::hasColumn('clinicas', 'dian_resolution_prefix')) {
                $table->string('dian_resolution_prefix', 20)->nullable()->after('dian_test_set_id');
            }
            if (! Schema::hasColumn('clinicas', 'dian_resolution_number')) {
                $table->string('dian_resolution_number', 50)->nullable()->after('dian_resolution_prefix');
            }
            if (! Schema::hasColumn('clinicas', 'dian_resolution_from')) {
                $table->integer('dian_resolution_from')->nullable()->after('dian_resolution_number');
            }
            if (! Schema::hasColumn('clinicas', 'dian_resolution_to')) {
                $table->integer('dian_resolution_to')->nullable()->after('dian_resolution_from');
            }
            if (! Schema::hasColumn('clinicas', 'dian_resolution_date')) {
                $table->date('dian_resolution_date')->nullable()->after('dian_resolution_to');
            }
            if (! Schema::hasColumn('clinicas', 'timezone')) {
                $table->string('timezone', 191)->default('America/Bogota')->after('dian_resolution_date');
            }
            if (! Schema::hasColumn('clinicas', 'currency')) {
                $table->string('currency', 10)->default('COP')->after('timezone');
            }
            if (! Schema::hasColumn('clinicas', 'meta')) {
                $table->json('meta')->nullable()->after('currency');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinicas', function (Blueprint $table) {
            $columns = [
                'name',
                'dv',
                'regimen',
                'responsable_iva',
                'phone',
                'address',
                'city',
                'department',
                'country',
                'website',
                'logo_path',
                'logo_dark_path',
                'primary_color',
                'secondary_color',
                'footer_note',
                'header_note',
                'payment_terms',
                'payment_due_days',
                'invoice_prefix',
                'invoice_footer_legal',
                'default_tax_rate',
                'bank_account_info',
                'whatsapp_number',
                'dian_enabled',
                'dian_software_id',
                'dian_software_pin',
                'dian_test_set_id',
                'dian_resolution_prefix',
                'dian_resolution_number',
                'dian_resolution_from',
                'dian_resolution_to',
                'dian_resolution_date',
                'timezone',
                'currency',
                'meta',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('clinicas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
