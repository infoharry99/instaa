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
        Schema::create('lead_tag_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_lead_id')->constrained('business_leads')->cascadeOnDelete();
            $table->foreignId('lead_tag_id')->constrained('lead_tags')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['business_lead_id', 'lead_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_tag_assignments');
    }
};
