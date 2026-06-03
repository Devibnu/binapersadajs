<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->string('header_background')->nullable();
            $table->string('footer_background')->nullable();
            $table->string('header_color', 20)->default('#0c1e35');
            $table->string('footer_color', 20)->default('#0c1e35');
            $table->string('button_color', 20)->default('#1f8f5f');
            $table->string('text_color', 20)->default('#263544');
            $table->longText('header_html')->nullable();
            $table->longText('footer_html')->nullable();
            $table->longText('disclaimer_html')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
