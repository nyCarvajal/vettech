<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_campaign_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('owners')->cascadeOnDelete();
            $table->string('campaign_type', 50)->index();
            $table->text('message');
            $table->timestamp('sent_at')->nullable()->index();
            $table->string('status', 30)->index();
            $table->text('response')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['patient_id', 'owner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_campaign_logs');
    }
};
