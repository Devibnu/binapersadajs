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

        Schema::table('homepage_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('homepage_settings', 'project_section_label')) {
                $table->string('project_section_label')->nullable()->after('cta_button_link');
            }
            if (! Schema::hasColumn('homepage_settings', 'project_section_title')) {
                $table->string('project_section_title')->nullable()->after('project_section_label');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('homepage_settings')) {
            return;
        }

        Schema::table('homepage_settings', function (Blueprint $table) {
            if (Schema::hasColumn('homepage_settings', 'project_section_title')) {
                $table->dropColumn('project_section_title');
            }
            if (Schema::hasColumn('homepage_settings', 'project_section_label')) {
                $table->dropColumn('project_section_label');
            }
        });
    }
};
