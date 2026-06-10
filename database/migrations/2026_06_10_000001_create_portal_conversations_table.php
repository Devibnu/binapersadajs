<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('module_type', 50);
            $table->unsignedBigInteger('module_id');
            $table->enum('sender_type', ['admin', 'client']);
            $table->unsignedBigInteger('sender_id');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['module_type', 'module_id']);
            $table->index(['sender_type', 'is_read']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_conversations');
    }
};
