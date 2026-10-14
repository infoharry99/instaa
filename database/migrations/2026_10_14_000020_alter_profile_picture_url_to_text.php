<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            DB::statement('ALTER TABLE connected_social_accounts MODIFY profile_picture_url TEXT NULL');
        } catch (\Throwable $e) {
            // Ignore if already text
        }

        try {
            DB::statement('ALTER TABLE business_leads MODIFY profile_picture_url TEXT NULL');
        } catch (\Throwable $e) {
            // Ignore if already text
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed
    }
};
