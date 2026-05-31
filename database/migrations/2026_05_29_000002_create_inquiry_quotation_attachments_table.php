<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inquiry_quotation_attachments')) {
            Schema::create('inquiry_quotation_attachments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('inquiry_quotation_id');
                $table->string('file_name', 255);
                $table->string('original_name', 255);
                $table->string('file_path', 255);
                $table->string('file_type', 50); // pdf, image, etc
                $table->string('mime_type', 100);
                $table->unsignedBigInteger('file_size');
                $table->enum('attachment_type', ['survey_photo', 'survey_document', 'quotation_pdf', 'client_document', 'other']);
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->timestamps();

                $table->index(['inquiry_quotation_id']);
                $table->index(['attachment_type']);
            });
        }

        // Try to add foreign key safely — ignore failures (constraint exists or missing parent table)
        try {
            if (Schema::hasTable('inquiry_quotations')) {
                DB::statement('ALTER TABLE inquiry_quotation_attachments ADD CONSTRAINT fk_iqa_inquiry_id FOREIGN KEY (inquiry_quotation_id) REFERENCES inquiry_quotations(id) ON DELETE CASCADE');
            }
        } catch (\Exception $e) {
            // constraint likely exists or cannot be added now — safe to ignore
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiry_quotation_attachments');
    }
};
