<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->longText('content')->nullable()->after('description');
            $table->text('short_content')->nullable()->after('content');
            $table->string('feature_1')->nullable()->after('short_content');
            $table->string('feature_2')->nullable()->after('feature_1');
            $table->string('feature_3')->nullable()->after('feature_2');
            $table->string('feature_4')->nullable()->after('feature_3');
            $table->string('gallery_image_1')->nullable()->after('feature_4');
            $table->string('gallery_image_2')->nullable()->after('gallery_image_1');
            $table->string('gallery_image_3')->nullable()->after('gallery_image_2');
            $table->string('faq_1_question')->nullable()->after('gallery_image_3');
            $table->text('faq_1_answer')->nullable()->after('faq_1_question');
            $table->string('faq_2_question')->nullable()->after('faq_1_answer');
            $table->text('faq_2_answer')->nullable()->after('faq_2_question');
            $table->string('faq_3_question')->nullable()->after('faq_2_answer');
            $table->text('faq_3_answer')->nullable()->after('faq_3_question');
            $table->string('cta_text')->nullable()->after('faq_3_answer');
            $table->string('cta_button_text')->nullable()->after('cta_text');
            $table->string('cta_button_link')->nullable()->after('cta_button_text');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'content',
                'short_content',
                'feature_1',
                'feature_2',
                'feature_3',
                'feature_4',
                'gallery_image_1',
                'gallery_image_2',
                'gallery_image_3',
                'faq_1_question',
                'faq_1_answer',
                'faq_2_question',
                'faq_2_answer',
                'faq_3_question',
                'faq_3_answer',
                'cta_text',
                'cta_button_text',
                'cta_button_link',
            ]);
        });
    }
};
