<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('homepage_settings')) {
            return;
        }

        if (! Schema::hasColumn('homepage_settings', 'service_section_label')) {
            Schema::table('homepage_settings', function (Blueprint $table) {
                $table->string('service_section_label')->nullable()->after('counter_4_icon');
            });
        }

        if (! Schema::hasColumn('homepage_settings', 'service_section_title')) {
            Schema::table('homepage_settings', function (Blueprint $table) {
                $table->string('service_section_title')->nullable()->after('service_section_label');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('homepage_settings')) {
            return;
        }

        $columns = array_values(array_filter(
            ['service_section_label', 'service_section_title'],
            fn (string $column): bool => Schema::hasColumn('homepage_settings', $column)
        ));

        if ($columns !== []) {
            Schema::table('homepage_settings', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
