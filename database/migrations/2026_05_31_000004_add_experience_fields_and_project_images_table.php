<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'scope_of_work')) {
                $table->longText('scope_of_work')->nullable()->after('description');
            }

            if (! Schema::hasColumn('projects', 'project_status')) {
                $table->string('project_status')->default('completed')->after('project_category_id');
            }

            if (! Schema::hasColumn('projects', 'progress')) {
                $table->unsignedTinyInteger('progress')->default(100)->after('project_status');
            }

            if (! Schema::hasColumn('projects', 'project_value')) {
                $table->decimal('project_value', 18, 2)->nullable()->after('progress');
            }
        });

        if (! Schema::hasTable('project_images')) {
            Schema::create('project_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained()->cascadeOnDelete();
                $table->string('image_path');
                $table->string('caption')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_images');

        Schema::table('projects', function (Blueprint $table) {
            foreach (['project_value', 'progress', 'project_status', 'scope_of_work'] as $column) {
                if (Schema::hasColumn('projects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
