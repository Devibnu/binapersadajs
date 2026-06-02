<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_reports', function (Blueprint $table) {
            $table->id();
            $table->string('project_no')->nullable();
            $table->string('job_title');
            $table->decimal('quotation_price', 18, 2)->nullable();
            $table->string('contract_number')->nullable();
            $table->decimal('contract_price', 18, 2)->nullable();
            $table->decimal('invoice_amount', 18, 2)->nullable();
            $table->string('corporation')->nullable();
            $table->string('department')->nullable();
            $table->string('user_pic')->nullable();
            $table->text('remark')->nullable();
            $table->string('e_wo_status')->nullable();
            $table->string('report_status')->nullable();
            $table->enum('visibility', ['private', 'public'])->default('private');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'visibility', 'sort_order']);
            $table->index('project_no');
        });

        Schema::create('project_report_iqm_user', function (Blueprint $table) {
            $table->foreignId('project_report_id')->constrained('project_reports')->cascadeOnDelete();
            $table->foreignId('iqm_user_id')->constrained('iqm_users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['project_report_id', 'iqm_user_id'], 'project_report_iqm_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_report_iqm_user');
        Schema::dropIfExists('project_reports');
    }
};
