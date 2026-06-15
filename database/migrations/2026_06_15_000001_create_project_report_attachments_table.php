<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_report_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_report_id')->constrained('project_reports')->cascadeOnDelete();
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('file_type', 50);
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->index('project_report_id');
            $table->index('file_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_report_attachments');
    }
};
