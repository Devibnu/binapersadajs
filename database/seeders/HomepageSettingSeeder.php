<?php

namespace Database\Seeders;

use App\Models\HomepageSetting;
use Illuminate\Database\Seeder;

class HomepageSettingSeeder extends Seeder
{
    public function run(): void
    {
        HomepageSetting::query()->firstOrCreate([], HomepageSetting::defaults());
    }
}
