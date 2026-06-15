<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_report_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_report_id')->constrained('invoice_reports')->cascadeOnDelete();
            $table->string('original_name');
            $table->string('file_path');
            $table->string('file_type', 50);
            $table->unsignedBigInteger('file_size');
            $table->timestamps();

            $table->index('invoice_report_id');
            $table->index('file_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_report_attachments');
    }
};
