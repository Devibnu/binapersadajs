<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_settings', function (Blueprint $table) {
            $table->id();
            $table->string('about_label')->nullable();
            $table->string('about_title')->nullable();
            $table->text('about_description')->nullable();
            $table->string('about_feature_1_title')->nullable();
            $table->string('about_feature_1_icon')->nullable();
            $table->string('about_feature_2_title')->nullable();
            $table->string('about_feature_2_icon')->nullable();
            $table->string('about_feature_3_title')->nullable();
            $table->string('about_feature_3_icon')->nullable();
            $table->string('about_feature_4_title')->nullable();
            $table->string('about_feature_4_icon')->nullable();
            $table->string('values_title')->nullable();
            $table->text('values_description')->nullable();
            $table->string('value_1_title')->nullable();
            $table->text('value_1_description')->nullable();
            $table->string('value_2_title')->nullable();
            $table->text('value_2_description')->nullable();
            $table->string('value_3_title')->nullable();
            $table->text('value_3_description')->nullable();
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
            $table->string('quality_title')->nullable();
            $table->text('quality_description')->nullable();
            $table->text('quality_sub_description')->nullable();
            $table->string('quality_item_1')->nullable();
            $table->string('quality_item_2')->nullable();
            $table->string('quality_item_3')->nullable();
            $table->string('quality_item_4')->nullable();
            $table->string('cta_phone_label')->nullable();
            $table->string('cta_phone')->nullable();
            $table->string('cta_title')->nullable();
            $table->text('cta_description')->nullable();
            $table->string('cta_button_text')->nullable();
            $table->string('cta_button_link')->nullable();
            $table->string('blog_label')->nullable();
            $table->string('blog_title')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_settings');
    }
};
