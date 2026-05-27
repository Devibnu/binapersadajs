<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_comments', function (Blueprint $table) {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('blog_id')
                ->constrained('blog_comments')
                ->cascadeOnDelete();
            $table->boolean('is_admin_reply')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('blog_comments', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'is_admin_reply']);
        });
    }
};
