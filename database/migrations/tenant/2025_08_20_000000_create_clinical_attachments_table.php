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
        Schema::create('clinical_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('historia_id');
            $table->unsignedBigInteger('paciente_id');
            $table->string('titulo');
            $table->string('titulo_limpio');
            $table->enum('file_type', ['image', 'pdf', 'video']);
            $table->string('mime_type');
            $table->unsignedBigInteger('size_bytes');
            $table->string('cloudinary_public_id');
            $table->text('cloudinary_secure_url');
            $table->string('cloudinary_resource_type');
            $table->string('cloudinary_format')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->decimal('duration', 8, 2)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('historia_id');
            $table->index('paciente_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_attachments');
    }
};
