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
        Schema::create('communication_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_lead_id')->constrained('business_leads')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('channel', ['email', 'call', 'whatsapp', 'instagram_manual', 'instagram_api']);
            $table->enum('direction', ['outbound', 'inbound'])->default('outbound');
            $table->string('subject')->nullable();
            $table->text('notes')->nullable();
            $table->string('outcome')->nullable(); // answered, voicemail, sent, replied, opened, etc.
            $table->string('contact_info')->nullable(); // target email or phone
            $table->timestamp('logged_at')->useCurrent();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'channel']);
            $table->index(['business_lead_id', 'logged_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communication_logs');
    }
};
