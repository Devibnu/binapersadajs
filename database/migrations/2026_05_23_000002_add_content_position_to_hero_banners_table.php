<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hero_banners') && ! Schema::hasColumn('hero_banners', 'content_position')) {
            Schema::table('hero_banners', function (Blueprint $table) {
                $table->string('content_position')->nullable()->after('sort_order');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hero_banners') && Schema::hasColumn('hero_banners', 'content_position')) {
            Schema::table('hero_banners', function (Blueprint $table) {
                $table->dropColumn('content_position');
            });
        }
    }
};
