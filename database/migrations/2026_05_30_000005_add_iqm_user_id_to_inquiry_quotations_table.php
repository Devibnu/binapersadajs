<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('inquiry_quotations', 'iqm_user_id')) {
            return;
        }

        Schema::table('inquiry_quotations', function (Blueprint $table) {
            $table->foreignId('iqm_user_id')
                ->nullable()
                ->after('client_logo')
                ->constrained('iqm_users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('inquiry_quotations', 'iqm_user_id')) {
            return;
        }

        Schema::table('inquiry_quotations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('iqm_user_id');
        });
    }
};
