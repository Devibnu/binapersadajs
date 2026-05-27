<?php

namespace Database\Seeders;

use App\Models\HomepageVideo;
use Illuminate\Database\Seeder;

class HomepageVideoSeeder extends Seeder
{
    public function run(): void
    {
        HomepageVideo::query()->firstOrCreate([], HomepageVideo::defaults());
    }
}
