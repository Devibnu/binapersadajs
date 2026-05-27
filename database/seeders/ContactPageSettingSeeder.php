<?php

namespace Database\Seeders;

use App\Models\ContactPageSetting;
use Illuminate\Database\Seeder;

class ContactPageSettingSeeder extends Seeder
{
    public function run(): void
    {
        ContactPageSetting::query()->firstOrCreate([], ContactPageSetting::defaults());
    }
}
