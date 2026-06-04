<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('smtp_host');
            $table->unsignedInteger('smtp_port')->default(587);
            $table->string('smtp_username');
            $table->text('smtp_password');
            $table->string('smtp_encryption', 20)->nullable();
            $table->string('imap_host')->nullable();
            $table->unsignedInteger('imap_port')->nullable();
            $table->string('imap_username')->nullable();
            $table->text('imap_password')->nullable();
            $table->string('imap_encryption', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('email_center_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_account_id')->nullable()->constrained('email_accounts')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('folder', ['sent', 'draft', 'trash'])->default('draft');
            $table->string('from_email')->nullable();
            $table->text('to_email')->nullable();
            $table->text('cc')->nullable();
            $table->text('bcc')->nullable();
            $table->string('subject')->nullable();
            $table->longText('body')->nullable();
            $table->boolean('use_template')->default(true);
            $table->string('status', 50)->default('draft');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('email_center_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_center_message_id')->constrained('email_center_messages')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_center_attachments');
        Schema::dropIfExists('email_center_messages');
        Schema::dropIfExists('email_accounts');
    }
};
