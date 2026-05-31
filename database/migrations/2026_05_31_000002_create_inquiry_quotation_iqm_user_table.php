<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiry_quotation_iqm_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_quotation_id')->constrained('inquiry_quotations')->cascadeOnDelete();
            $table->foreignId('iqm_user_id')->constrained('iqm_users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['inquiry_quotation_id', 'iqm_user_id'], 'iqm_inquiry_user_unique');
        });

        DB::table('inquiry_quotations')
            ->whereNotNull('iqm_user_id')
            ->orderBy('id')
            ->select(['id', 'iqm_user_id'])
            ->chunkById(100, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('inquiry_quotation_iqm_user')->updateOrInsert(
                        [
                            'inquiry_quotation_id' => $row->id,
                            'iqm_user_id' => $row->iqm_user_id,
                        ],
                        [
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiry_quotation_iqm_user');
    }
};
