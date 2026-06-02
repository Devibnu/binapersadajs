<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_reports', function (Blueprint $table) {
            $table->id();
            $table->string('client');
            $table->string('invoice_no')->nullable();
            $table->string('po_wo_no')->nullable();
            $table->string('job_title');
            $table->date('invoice_date')->nullable();
            $table->decimal('quantity', 18, 2)->nullable();
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 18, 2)->nullable();
            $table->decimal('total_amount', 18, 2)->nullable();
            $table->enum('visibility', ['private', 'public'])->default('private');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'visibility', 'sort_order']);
            $table->index('invoice_no');
        });

        Schema::create('invoice_report_iqm_user', function (Blueprint $table) {
            $table->foreignId('invoice_report_id')->constrained('invoice_reports')->cascadeOnDelete();
            $table->foreignId('iqm_user_id')->constrained('iqm_users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['invoice_report_id', 'iqm_user_id'], 'invoice_report_iqm_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_report_iqm_user');
        Schema::dropIfExists('invoice_reports');
    }
};
