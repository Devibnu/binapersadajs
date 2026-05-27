<?php

namespace Database\Seeders;

use App\Models\AboutPageSetting;
use Illuminate\Database\Seeder;

class AboutPageSettingSeeder extends Seeder
{
    public function run(): void
    {
        AboutPageSetting::query()->firstOrCreate([], AboutPageSetting::defaults());
    }
}
