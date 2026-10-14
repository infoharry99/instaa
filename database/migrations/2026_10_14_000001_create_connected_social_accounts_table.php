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
        Schema::create('connected_social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('platform')->default('instagram'); // instagram, facebook
            $table->string('account_id')->index(); // IG Business ID or Page ID
            $table->string('username')->nullable();
            $table->string('name')->nullable();
            $table->string('profile_picture_url')->nullable();
            $table->string('account_type')->default('BUSINESS'); // BUSINESS, CREATOR
            $table->text('access_token');
            $table->timestamp('token_expires_at')->nullable();
            $table->boolean('is_connected')->default(true);
            $table->json('scopes')->nullable();
            $table->json('meta_data')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'platform', 'account_id'], 'company_social_acc_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('connected_social_accounts');
    }
};
