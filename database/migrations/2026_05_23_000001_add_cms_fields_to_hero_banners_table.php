<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('hero_banners')) {
            Schema::create('hero_banners', function (Blueprint $table) {
                $table->id();
                $table->string('small_text')->nullable();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('button_text')->nullable();
                $table->string('button_link')->nullable();
                $table->string('image')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });

            return;
        }

        Schema::table('hero_banners', function (Blueprint $table) {
            if (! Schema::hasColumn('hero_banners', 'small_text')) {
                $table->string('small_text')->nullable()->after('id');
            }

            if (! Schema::hasColumn('hero_banners', 'title')) {
                $table->string('title')->nullable()->after('small_text');
            }

            if (! Schema::hasColumn('hero_banners', 'description')) {
                $table->text('description')->nullable()->after('title');
            }

            if (! Schema::hasColumn('hero_banners', 'button_text')) {
                $table->string('button_text')->nullable()->after('description');
            }

            if (! Schema::hasColumn('hero_banners', 'button_link')) {
                $table->string('button_link')->nullable()->after('button_text');
            }

            if (! Schema::hasColumn('hero_banners', 'image')) {
                $table->string('image')->nullable()->after('button_link');
            }

            if (! Schema::hasColumn('hero_banners', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('image');
            }

            if (! Schema::hasColumn('hero_banners', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('is_active');
            }
        });

        if (Schema::hasColumn('hero_banners', 'judul')) {
            DB::statement('UPDATE hero_banners SET title = COALESCE(title, judul)');
        }

        if (Schema::hasColumn('hero_banners', 'sub_judul')) {
            DB::statement('UPDATE hero_banners SET small_text = COALESCE(small_text, sub_judul)');
        }

        if (Schema::hasColumn('hero_banners', 'teks_tombol')) {
            DB::statement('UPDATE hero_banners SET button_text = COALESCE(button_text, teks_tombol)');
        }

        if (Schema::hasColumn('hero_banners', 'link_tombol')) {
            DB::statement('UPDATE hero_banners SET button_link = COALESCE(button_link, link_tombol)');
        }

        if (Schema::hasColumn('hero_banners', 'gambar_background')) {
            DB::statement('UPDATE hero_banners SET image = COALESCE(image, gambar_background)');
        }

        if (Schema::hasColumn('hero_banners', 'status_aktif')) {
            DB::statement('UPDATE hero_banners SET is_active = status_aktif');
        }

        if (Schema::hasColumn('hero_banners', 'urutan')) {
            DB::statement('UPDATE hero_banners SET sort_order = urutan');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('hero_banners')) {
            return;
        }

        Schema::table('hero_banners', function (Blueprint $table) {
            foreach ([
                'small_text',
                'title',
                'description',
                'button_text',
                'button_link',
                'image',
                'is_active',
                'sort_order',
            ] as $column) {
                if (Schema::hasColumn('hero_banners', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
