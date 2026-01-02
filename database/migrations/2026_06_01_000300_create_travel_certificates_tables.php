<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('code')->unique();
            $table->enum('type', ['national_co', 'international']);
            $table->enum('status', ['draft', 'issued', 'canceled'])->default('draft');
            $table->dateTime('issued_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->string('clinic_name');
            $table->string('clinic_nit');
            $table->string('clinic_address');
            $table->string('clinic_phone')->nullable();
            $table->string('clinic_city')->nullable();
            $table->string('vet_name');
            $table->string('vet_license');
            $table->string('vet_signature_path')->nullable();
            $table->string('vet_seal_path')->nullable();
            $table->string('owner_name');
            $table->string('owner_document_type')->nullable();
            $table->string('owner_document_number')->nullable();
            $table->string('owner_phone')->nullable();
            $table->string('owner_email')->nullable();
            $table->string('owner_address')->nullable();
            $table->string('owner_city')->nullable();
            $table->unsignedBigInteger('pet_id')->nullable();
            $table->string('pet_name');
            $table->string('pet_species');
            $table->string('pet_breed')->nullable();
            $table->string('pet_sex')->nullable();
            $table->integer('pet_age_months')->nullable();
            $table->decimal('pet_weight_kg', 8, 2)->nullable();
            $table->string('pet_color')->nullable();
            $table->string('pet_marks')->nullable();
            $table->string('pet_microchip')->nullable();
            $table->date('travel_departure_date');
            $table->time('travel_departure_time')->nullable();
            $table->enum('transport_type', ['air', 'land', 'other'])->nullable();
            $table->string('transport_company')->nullable();
            $table->string('flight_number')->nullable();
            $table->enum('origin_type', ['co', 'international'])->nullable();
            $table->string('origin_country_code', 2)->nullable();
            $table->string('origin_city')->nullable();
            $table->bigInteger('origin_department_id')->nullable();
            $table->bigInteger('origin_municipality_id')->nullable();
            $table->string('destination_country_code', 2)->nullable();
            $table->string('destination_city')->nullable();
            $table->bigInteger('destination_department_id')->nullable();
            $table->bigInteger('destination_municipality_id')->nullable();
            $table->dateTime('clinical_exam_at');
            $table->text('clinical_notes')->nullable();
            $table->boolean('fit_for_travel')->default(true);
            $table->text('declaration_text');
            $table->enum('language', ['es', 'en', 'es_en'])->default('es');
            $table->json('extras')->nullable();
            $table->text('canceled_reason')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        if (Schema::hasTable('departamentos')) {
            Schema::table('travel_certificates', function (Blueprint $table) {
                $table->foreign('origin_department_id')->references('id')->on('departamentos');
                $table->foreign('destination_department_id')->references('id')->on('departamentos');
            });
        }

        if (Schema::hasTable('municipios')) {
            Schema::table('travel_certificates', function (Blueprint $table) {
                $table->foreign('origin_municipality_id')->references('id')->on('municipios');
                $table->foreign('destination_municipality_id')->references('id')->on('municipios');
            });
        }

        Schema::create('travel_certificate_vaccinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_certificate_id')->constrained()->cascadeOnDelete();
            $table->string('vaccine_name');
            $table->string('product_name')->nullable();
            $table->string('batch_lot')->nullable();
            $table->date('applied_at');
            $table->date('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('travel_certificate_dewormings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_certificate_id')->constrained()->cascadeOnDelete();
            $table->enum('kind', ['internal', 'external']);
            $table->string('product_name');
            $table->string('active_ingredient')->nullable();
            $table->string('batch_lot')->nullable();
            $table->date('applied_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('travel_certificate_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_certificate_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->string('mime');
            $table->unsignedBigInteger('size_bytes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_certificate_attachments');
        Schema::dropIfExists('travel_certificate_dewormings');
        Schema::dropIfExists('travel_certificate_vaccinations');
        Schema::dropIfExists('travel_certificates');
    }
};
