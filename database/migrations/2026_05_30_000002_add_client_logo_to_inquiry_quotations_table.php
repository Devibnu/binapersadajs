<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiry_quotations', function (Blueprint $table) {
            $table->string('client_logo')->nullable()->after('client_email');
        });
    }

    public function down(): void
    {
        Schema::table('inquiry_quotations', function (Blueprint $table) {
            $table->dropColumn('client_logo');
        });
    }
};
