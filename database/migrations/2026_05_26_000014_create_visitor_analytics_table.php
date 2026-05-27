<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('url');
            $table->string('path');
            $table->string('page_title')->nullable();
            $table->text('referer')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('browser')->nullable()->index();
            $table->string('platform')->nullable()->index();
            $table->string('device_type')->nullable()->index();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->timestamp('visited_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_analytics');
    }
};
