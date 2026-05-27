<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hero_title')->nullable();
            $table->string('hero_breadcrumb')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('section_label')->nullable();
            $table->string('section_title')->nullable();
            $table->text('section_description')->nullable();
            $table->text('quote_text')->nullable();
            $table->text('section_description_bottom')->nullable();
            $table->string('slider_1_title')->nullable();
            $table->string('slider_1_image')->nullable();
            $table->string('slider_2_title')->nullable();
            $table->string('slider_2_image')->nullable();
            $table->string('slider_3_title')->nullable();
            $table->string('slider_3_image')->nullable();
            $table->string('counter_1_number')->nullable();
            $table->string('counter_1_label')->nullable();
            $table->string('counter_1_icon')->nullable();
            $table->string('counter_2_number')->nullable();
            $table->string('counter_2_label')->nullable();
            $table->string('counter_2_icon')->nullable();
            $table->string('counter_3_number')->nullable();
            $table->string('counter_3_label')->nullable();
            $table->string('counter_3_icon')->nullable();
            $table->string('counter_4_number')->nullable();
            $table->string('counter_4_label')->nullable();
            $table->string('counter_4_icon')->nullable();
            $table->string('team_label')->nullable();
            $table->string('team_title')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_page_settings');
    }
};
