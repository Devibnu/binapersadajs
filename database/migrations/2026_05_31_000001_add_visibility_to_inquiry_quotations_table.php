<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('inquiry_quotations', 'visibility')) {
            return;
        }

        Schema::table('inquiry_quotations', function (Blueprint $table) {
            $table->enum('visibility', ['private', 'public'])
                ->default('private')
                ->after('iqm_user_id')
                ->index();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('inquiry_quotations', 'visibility')) {
            return;
        }

        Schema::table('inquiry_quotations', function (Blueprint $table) {
            $table->dropColumn('visibility');
        });
    }
};
