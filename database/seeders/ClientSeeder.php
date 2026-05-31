<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            'PT. Sankyu Indonesia International',
            'PT. Nippon Shokubai Indonesia',
            'PT. Asahimas Chemical',
            'PT. Lautan Otsuka Chemical',
            'PT. Jawa Manis Rafinasi',
            'PT. Multimas Nabati Asahan',
            'PT. Elektrindo Utama Indonesia',
            'PT. Vopak',
            'PT. Rekayasa Industri',
            'PT. Paul Wurth Italia',
            'PT. JGC Indonesia',
            'PT. Semen Merah Putih',
        ];

        foreach ($clients as $index => $name) {
            Client::updateOrCreate(['name' => $name], [
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }
    }
}
