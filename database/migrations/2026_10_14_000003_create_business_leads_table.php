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
        Schema::create('business_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('lead_source_id')->nullable()->constrained('lead_sources')->nullOnDelete();
            $table->string('business_name')->index();
            $table->string('instagram_username')->nullable()->index();
            $table->string('instagram_profile_url')->nullable();
            $table->string('profile_picture_url', 500)->nullable();
            $table->text('business_description')->nullable();
            $table->string('category')->index();
            $table->string('city')->nullable()->index();
            $table->string('state')->nullable();
            $table->string('country')->default('India');
            $table->string('location_address')->nullable();
            $table->string('official_website')->nullable();
            $table->string('public_email')->nullable()->index();
            $table->string('public_phone')->nullable()->index();
            $table->string('contact_person')->nullable();
            $table->enum('status', ['new', 'contacted', 'interested', 'follow_up', 'converted', 'not_interested'])->default('new')->index();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->index();
            $table->string('source_type')->default('instagram_discovery');
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'category']);
            $table->index(['company_id', 'city']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_leads');
    }
};
