<?php

namespace App\Http\Controllers;

use App\Models\PageHero;
use Illuminate\Support\Facades\Schema;

class WebsiteBlogController extends Controller
{
    public function index()
    {
        return view('website.blog.index', [
            'posts' => $this->posts(),
            'pageHero' => $this->pageHero('blog'),
        ]);
    }

    private function pageHero(string $pageKey): ?PageHero
    {
        if (! Schema::hasTable('page_heroes')) {
            return null;
        }

        return PageHero::where('page_key', $pageKey)
            ->where('is_active', true)
            ->first();
    }

    public function show(string $slug)
    {
        $posts = $this->posts();
        $post = collect($posts)->firstWhere('slug', $slug);

        abort_if(! $post, 404);

        return view('website.blog.show', [
            'post' => $post,
            'relatedPosts' => collect($posts)
                ->where('slug', '!=', $slug)
                ->take(2)
                ->values(),
        ]);
    }

    private function posts(): array
    {
        return [
            [
                'title' => 'Project Maintenance PT Sankyu',
                'slug' => 'project-maintenance-pt-sankyu',
                'date' => '22 May 2026',
                'category' => 'Project Update',
                'image' => 'web/images/news/news1.jpg',
                'excerpt' => 'Update pekerjaan maintenance area industri dengan fokus pada reliability equipment, safety control, dan koordinasi lapangan.',
                'content' => [
                    'PT Bina Persada JS menjalankan pekerjaan maintenance untuk mendukung kelancaran operasional area industri PT Sankyu. Aktivitas dilakukan melalui inspeksi awal, perencanaan tenaga kerja, pengaturan material, dan pelaksanaan pekerjaan sesuai prosedur keselamatan.',
                    'Tim lapangan memastikan setiap tahapan maintenance berjalan terukur, mulai dari pengecekan kondisi equipment, preventive action, hingga dokumentasi hasil pekerjaan. Pendekatan ini membantu menekan risiko downtime dan menjaga produktivitas area kerja.',
                    'Koordinasi harian bersama pihak terkait menjadi bagian penting dari pekerjaan ini agar target mutu, waktu, dan keselamatan dapat tercapai dengan baik.',
                ],
            ],
            [
                'title' => 'Safety Work Procedure in Industrial Area',
                'slug' => 'safety-work-procedure-in-industrial-area',
                'date' => '18 May 2026',
                'category' => 'Safety & HSE',
                'image' => 'web/images/news/news2.jpg',
                'excerpt' => 'Ringkasan prosedur keselamatan kerja untuk pekerjaan fabrication, maintenance, dan installation di lingkungan industri.',
                'content' => [
                    'Keselamatan kerja menjadi fondasi utama dalam setiap aktivitas PT Bina Persada JS. Sebelum pekerjaan dimulai, tim melakukan toolbox meeting, pemeriksaan alat kerja, validasi izin kerja, dan identifikasi potensi bahaya di area kerja.',
                    'Pekerjaan di area industri membutuhkan disiplin tinggi terhadap penggunaan APD, pengendalian energi, housekeeping, serta komunikasi antar pekerja. Setiap temuan risiko dicatat dan ditindaklanjuti sebelum pekerjaan dilanjutkan.',
                    'Dengan penerapan prosedur HSE yang konsisten, produktivitas dapat berjalan berdampingan dengan perlindungan tenaga kerja dan aset pelanggan.',
                ],
            ],
            [
                'title' => 'Fabrication & Pipe Installation Project',
                'slug' => 'fabrication-pipe-installation-project',
                'date' => '12 May 2026',
                'category' => 'Fabrication',
                'image' => 'web/images/news/news3.jpg',
                'excerpt' => 'Dokumentasi singkat pekerjaan fabrikasi dan instalasi pipa, termasuk persiapan material, fit-up, welding, dan final inspection.',
                'content' => [
                    'Pekerjaan fabrication dan pipe installation dilakukan dengan tahapan yang rapi, mulai dari pembacaan drawing, pengukuran material, cutting, fit-up, welding, sampai pemasangan di lokasi kerja.',
                    'Setiap sambungan dan jalur pipa diperiksa untuk memastikan kesesuaian terhadap spesifikasi teknis. Tim juga menjaga area kerja tetap tertata agar proses instalasi berjalan efisien dan aman.',
                    'Dokumentasi hasil kerja menjadi bagian dari quality control sekaligus referensi untuk maintenance berikutnya.',
                ],
            ],
        ];
    }
}
