<?php

namespace Database\Seeders;

use App\Models\IqmUser;
use Illuminate\Database\Seeder;

class IqmUserSeeder extends Seeder
{
    public function run(): void
    {
        IqmUser::updateOrCreate(
            ['username' => 'admin@softui.com'],
            [
                'company_name' => 'Administrator',
                'pic_name' => 'Administrator',
                'email' => 'admin@softui.com',
                'phone' => null,
                'password' => '123456',
                'status' => 'active',
            ]
        );
    }
}
