<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_heroes', function (Blueprint $table) {
            $table->id();
            $table->string('page_key', 100)->unique();
            $table->string('title');
            $table->string('breadcrumb_text')->nullable();
            $table->string('background_image')->nullable();
            $table->decimal('overlay_opacity', 3, 2)->nullable()->default(1);
            $table->string('text_position')->nullable()->default('center');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_heroes');
    }
};
