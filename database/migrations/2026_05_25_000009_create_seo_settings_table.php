<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('og_image')->nullable();
            $table->string('canonical_url')->nullable();
            $table->boolean('robots_index')->default(true);
            $table->boolean('robots_follow')->default(true);
            $table->string('google_site_verification')->nullable();
            $table->string('google_analytics_id')->nullable();
            $table->string('google_tag_manager')->nullable();
            $table->string('schema_company_name')->nullable();
            $table->string('schema_logo')->nullable();
            $table->string('schema_phone')->nullable();
            $table->string('schema_email')->nullable();
            $table->text('schema_address')->nullable();
            $table->string('schema_city')->nullable();
            $table->string('schema_country')->nullable();
            $table->string('schema_postal_code')->nullable();
            $table->string('twitter_card_type')->nullable();
            $table->string('twitter_site')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
    }
};
