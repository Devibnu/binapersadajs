<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $blogs = [
            [
                'title' => 'Pentingnya Maintenance Berkala untuk Area Industri',
                'slug' => 'pentingnya-maintenance-berkala-untuk-area-industri',
                'excerpt' => 'Maintenance terencana membantu menjaga keandalan peralatan, mengurangi downtime, dan mendukung operasi industri yang aman.',
                'content' => '<p>Area industri membutuhkan peralatan yang siap beroperasi secara konsisten. Maintenance berkala membantu tim mengenali penurunan kondisi equipment sebelum berkembang menjadi gangguan yang menghambat produksi.</p><p>PT. Bina Persada Jaya Sejahtera mendukung pekerjaan <strong>preventive</strong> maupun <strong>corrective maintenance</strong> melalui persiapan tenaga kerja, alat, material, serta koordinasi pekerjaan yang mengutamakan keselamatan.</p><p>Dokumentasi inspeksi dan hasil pekerjaan juga menjadi bagian penting untuk membantu pelanggan merencanakan aktivitas pemeliharaan berikutnya dengan lebih terukur.</p>',
                'featured_image' => 'web/images/news/news1.jpg',
                'category' => 'Maintenance',
                'tags' => 'Maintenance, Industri, Safety',
                'author_name' => 'Admin',
                'published_at' => '2026-05-22 08:00:00',
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Dukungan Manpower untuk Project Shutdown',
                'slug' => 'dukungan-manpower-untuk-project-shutdown',
                'excerpt' => 'Project shutdown memerlukan tenaga kerja terampil, koordinasi cepat, dan disiplin HSE agar target waktu dapat tercapai.',
                'content' => '<p>Pekerjaan shutdown berlangsung dalam waktu terbatas dan membutuhkan kesiapan sumber daya sejak tahap awal. Tim lapangan perlu memahami ruang lingkup, pembagian area, izin kerja, serta target penyelesaian pekerjaan.</p><p>Dukungan manpower yang tepat membantu kegiatan dismantling, installation, maintenance, dan housekeeping berjalan terkoordinasi di bawah pengawasan keselamatan kerja.</p><p>Dengan koordinasi harian yang rapi, pelanggan dapat menjalankan kembali operasional fasilitas secara lebih terkendali setelah masa shutdown selesai.</p>',
                'featured_image' => 'web/images/news/news2.jpg',
                'category' => 'Project Update',
                'tags' => 'Manpower, Shutdown, HSE',
                'author_name' => 'Admin',
                'published_at' => '2026-05-18 08:00:00',
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Peran Fabrication dalam Kebutuhan Konstruksi Industri',
                'slug' => 'peran-fabrication-dalam-kebutuhan-konstruksi-industri',
                'excerpt' => 'Pekerjaan fabrication mendukung pembuatan struktur dan komponen yang presisi untuk kebutuhan instalasi industri.',
                'content' => '<p>Fabrication merupakan bagian penting dari pekerjaan konstruksi industri, mulai dari persiapan material, cutting, fit-up, welding, hingga inspeksi akhir sebelum instalasi di lapangan.</p><p>Ketelitian pada setiap tahap membantu menjaga kesesuaian dimensi, mutu sambungan, dan efisiensi proses pemasangan di area kerja.</p><p>PT. Bina Persada Jaya Sejahtera menyediakan dukungan fabrication dan installation dengan pendekatan kerja yang praktis, aman, dan sesuai kebutuhan project pelanggan.</p>',
                'featured_image' => 'web/images/news/news3.jpg',
                'category' => 'Fabrication',
                'tags' => 'Fabrication, Construction, Piping',
                'author_name' => 'Admin',
                'published_at' => '2026-05-12 08:00:00',
                'is_published' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($blogs as $blog) {
            Blog::updateOrCreate(['slug' => $blog['slug']], $blog);
        }
    }
}
