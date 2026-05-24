<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_banners', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('sub_judul')->nullable();
            $table->string('teks_tombol')->nullable();
            $table->string('link_tombol')->nullable();
            $table->string('gambar_background')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_banners');
    }
};
