<?php

namespace Database\Seeders;

use App\Models\HeroBanner;
use Illuminate\Database\Seeder;

class HeroBannerSeeder extends Seeder
{
    public function run(): void
    {
        HeroBanner::updateOrCreate(
            ['sort_order' => 1],
            [
                'small_text' => 'MAINTENANCE & PROJECT SUPPORT',
                'title' => 'RELIABLE SITE EXECUTION',
                'description' => 'We support fabrication, installation, and shutdown work with safety-focused teams and practical field coordination.',
                'button_text' => 'VIEW PROJECTS',
                'button_link' => '/projects',
                'image' => 'web/images/projects/parallax1.jpg',
                'is_active' => true,
                'sort_order' => 1,
                'content_position' => 'center',
                'judul' => 'RELIABLE SITE EXECUTION',
                'sub_judul' => 'MAINTENANCE & PROJECT SUPPORT',
                'teks_tombol' => 'VIEW PROJECTS',
                'link_tombol' => '/projects',
                'gambar_background' => 'web/images/projects/parallax1.jpg',
                'status_aktif' => true,
                'urutan' => 1,
            ]
        );

        HeroBanner::where('sort_order', 2)->update(['content_position' => 'left']);
        HeroBanner::where('sort_order', 3)->update(['content_position' => 'right']);
    }
}
