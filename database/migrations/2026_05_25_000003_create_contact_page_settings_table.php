<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('section_label')->nullable();
            $table->string('heading')->nullable();
            $table->string('address_title')->nullable();
            $table->string('email_title')->nullable();
            $table->string('phone_title')->nullable();
            $table->text('map_embed')->nullable();
            $table->string('form_heading')->nullable();
            $table->text('success_message')->nullable();
            $table->string('submit_button_text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_page_settings');
    }
};
