<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consent_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('category')->nullable();
            $table->longText('body_html');
            $table->boolean('is_active')->default(true);
            $table->json('allowed_placeholders')->nullable();
            $table->json('required_signers')->nullable();
            $table->boolean('requires_pet')->default(true);
            $table->boolean('requires_owner')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'is_active']);
        });

        Schema::create('consent_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('code')->unique();
            $table->string('status')->default('draft');
            $table->foreignId('template_id')->constrained('consent_templates');
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->unsignedBigInteger('pet_id')->nullable();
            $table->json('owner_snapshot')->nullable();
            $table->json('pet_snapshot')->nullable();
            $table->longText('merged_body_html');
            $table->longText('merged_plain_text')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->text('canceled_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('consent_signatures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->foreignId('consent_document_id')->constrained('consent_documents')->cascadeOnDelete();
            $table->string('signer_role');
            $table->string('signer_name');
            $table->string('signer_document')->nullable();
            $table->string('signature_image_path');
            $table->timestamp('signed_at');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('method');
            $table->string('geo_hint')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'consent_document_id']);
        });

        Schema::create('consent_public_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->foreignId('consent_document_id')->constrained('consent_documents')->cascadeOnDelete();
            $table->string('token_hash')->unique();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('max_uses')->default(1);
            $table->unsignedInteger('uses')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'expires_at']);
        });

        Schema::create('consent_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consent_document_id')->constrained('consent_documents')->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consent_attachments');
        Schema::dropIfExists('consent_public_links');
        Schema::dropIfExists('consent_signatures');
        Schema::dropIfExists('consent_documents');
        Schema::dropIfExists('consent_templates');
    }
};
