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
        Schema::create('lead_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('provider_type')->default('meta_api'); // meta_api, official_directory, outscraper, google_places, manual_import, user_entry
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('api_endpoint')->nullable();
            $table->unsignedInteger('rate_limit_per_minute')->default(60);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_sources');
    }
};
