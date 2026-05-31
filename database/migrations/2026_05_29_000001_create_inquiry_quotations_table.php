<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiry_quotations', function (Blueprint $table) {
            $table->id();
            
            // Inquiry Information
            $table->string('inquiry_number', 50)->unique(); // INQ-2026-0001
            $table->date('inquiry_date');
            $table->enum('inquiry_by', ['email', 'whatsapp', 'phone', 'site_instruction', 'meeting', 'referral', 'other']);
            
            // Client Information
            $table->string('client_name', 150);
            $table->string('client_pic', 150)->nullable();
            $table->string('client_phone', 30)->nullable();
            $table->string('client_email', 150)->nullable();
            $table->text('client_address')->nullable();
            
            // Subject & Description
            $table->string('subject', 200);
            $table->text('description')->nullable();
            $table->string('pic_internal', 150)->nullable(); // PIC dari internal
            
            // Site Survey
            $table->enum('site_survey_status', ['not_required', 'scheduled', 'done'])->default('not_required');
            $table->date('site_survey_date')->nullable();
            $table->text('site_survey_notes')->nullable();
            
            // Quotation
            $table->string('quotation_number', 50)->nullable()->unique(); // QTN-2026-0001
            $table->date('quotation_date')->nullable();
            $table->date('deadline')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->enum('quotation_status', ['draft', 'process', 'submitted', 'revision', 'approved', 'rejected', 'closed'])->default('draft');
            
            // Additional Notes
            $table->text('notes')->nullable();
            
            // Audit Fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['inquiry_number']);
            $table->index(['quotation_number']);
            $table->index(['client_name']);
            $table->index(['inquiry_by']);
            $table->index(['site_survey_status']);
            $table->index(['quotation_status']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiry_quotations');
    }
};
