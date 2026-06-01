<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            'Dashboard' => ['dashboard.view' => 'Lihat dashboard'],
            'Website Settings' => ['website-settings.view' => 'Lihat pengaturan website', 'website-settings.update' => 'Ubah pengaturan website'],
            'Contact Page' => ['contact-page.view' => 'Lihat contact page', 'contact-page.update' => 'Ubah contact page'],
            'Email Settings' => ['email-settings.view' => 'Lihat pengaturan email', 'email-settings.update' => 'Ubah pengaturan email', 'email-settings.test' => 'Tes email'],
            'Homepage Sections' => ['homepage-sections.view' => 'Lihat homepage sections', 'homepage-sections.update' => 'Ubah homepage sections'],
            'Homepage Video' => ['homepage-video.view' => 'Lihat homepage video', 'homepage-video.update' => 'Ubah homepage video'],
            'SEO Settings' => ['seo-settings.view' => 'Lihat SEO settings', 'seo-settings.update' => 'Ubah SEO settings'],
            'Media Library' => ['media-library.view' => 'Lihat media', 'media-library.upload' => 'Upload media', 'media-library.delete' => 'Hapus media'],
            'Activity Logs' => ['activity-logs.view' => 'Lihat activity logs'],
            'Analytics Visitor' => ['analytics.view' => 'Lihat statistik visitor'],
            'Leads' => ['leads.view' => 'Lihat leads', 'leads.update' => 'Ubah status leads', 'leads.delete' => 'Hapus leads'],
            'Inquiry & Quotation' => ['inquiry-quotation.view' => 'Lihat inquiry quotation', 'inquiry-quotation.create' => 'Tambah inquiry quotation', 'inquiry-quotation.edit' => 'Ubah inquiry quotation', 'inquiry-quotation.delete' => 'Hapus inquiry quotation'],
            'IQM' => ['iqm-user.view' => 'Lihat user portal IQM', 'iqm-user.create' => 'Tambah user portal IQM', 'iqm-user.edit' => 'Ubah user portal IQM', 'iqm-user.delete' => 'Hapus user portal IQM'],
            'About Page' => ['about-page.view' => 'Lihat about page', 'about-page.update' => 'Ubah about page'],
            'About Videos' => ['about-videos.view' => 'Lihat video about', 'about-videos.create' => 'Tambah video about', 'about-videos.update' => 'Ubah video about', 'about-videos.delete' => 'Hapus video about'],
            'Music Playlist' => ['music-playlists.view' => 'Lihat music playlist', 'music-playlists.create' => 'Tambah music playlist', 'music-playlists.update' => 'Ubah music playlist', 'music-playlists.delete' => 'Hapus music playlist'],
            'About Teams' => ['about-teams.view' => 'Lihat tim', 'about-teams.create' => 'Tambah tim', 'about-teams.update' => 'Ubah tim', 'about-teams.delete' => 'Hapus tim'],
            'Page Heroes' => ['page-heroes.view' => 'Lihat page hero', 'page-heroes.create' => 'Tambah page hero', 'page-heroes.update' => 'Ubah page hero', 'page-heroes.delete' => 'Hapus page hero'],
            'Hero Banner' => ['hero-banners.view' => 'Lihat hero banner', 'hero-banners.create' => 'Tambah hero banner', 'hero-banners.update' => 'Ubah hero banner', 'hero-banners.delete' => 'Hapus hero banner'],
            'Services' => ['services.view' => 'Lihat layanan', 'services.create' => 'Tambah layanan', 'services.update' => 'Ubah layanan', 'services.delete' => 'Hapus layanan'],
            'Project Categories' => ['project-categories.view' => 'Lihat kategori project', 'project-categories.create' => 'Tambah kategori project', 'project-categories.update' => 'Ubah kategori project', 'project-categories.delete' => 'Hapus kategori project'],
            'Projects' => ['projects.view' => 'Lihat project', 'projects.create' => 'Tambah project', 'projects.update' => 'Ubah project', 'projects.delete' => 'Hapus project'],
            'Clients' => ['clients.view' => 'Lihat clients', 'clients.create' => 'Tambah clients', 'clients.update' => 'Ubah clients', 'clients.delete' => 'Hapus clients'],
            'Blogs' => ['blogs.view' => 'Lihat blog', 'blogs.create' => 'Tambah blog', 'blogs.update' => 'Ubah blog', 'blogs.delete' => 'Hapus blog'],
            'Blog Comments' => ['blog-comments.view' => 'Lihat komentar', 'blog-comments.approve' => 'Setujui komentar', 'blog-comments.reject' => 'Tolak komentar', 'blog-comments.reply' => 'Balas komentar', 'blog-comments.delete' => 'Hapus komentar'],
            'Contact Messages' => ['contact-messages.view' => 'Lihat pesan kontak', 'contact-messages.read' => 'Tandai dibaca', 'contact-messages.reply' => 'Balas pesan', 'contact-messages.delete' => 'Hapus pesan'],
            'Users' => ['users.view' => 'Lihat user', 'users.create' => 'Tambah user', 'users.update' => 'Ubah user', 'users.delete' => 'Hapus user'],
            'Roles' => ['roles.view' => 'Lihat role', 'roles.create' => 'Tambah role', 'roles.update' => 'Ubah role', 'roles.delete' => 'Hapus role'],
        ];

        foreach ($definitions as $group => $permissions) {
            foreach ($permissions as $slug => $name) {
                Permission::updateOrCreate(['slug' => $slug], [
                    'name' => $name,
                    'group_name' => $group,
                ]);
            }
        }

        $superAdmin = Role::updateOrCreate(['slug' => 'super-admin'], [
            'name' => 'Super Admin',
            'description' => 'Akses penuh seluruh modul panel admin.',
            'is_super_admin' => true,
            'is_active' => true,
        ]);
        $contentAdmin = Role::updateOrCreate(['slug' => 'admin-content'], [
            'name' => 'Admin Content',
            'description' => 'Mengelola konten website dan media.',
            'is_super_admin' => false,
            'is_active' => true,
        ]);
        $supportAdmin = Role::updateOrCreate(['slug' => 'admin-support'], [
            'name' => 'Admin Support',
            'description' => 'Menangani pesan dan komentar pengunjung.',
            'is_super_admin' => false,
            'is_active' => true,
        ]);

        $superAdmin->permissions()->sync(Permission::pluck('id'));
        $contentAdmin->permissions()->sync(Permission::whereIn('slug', [
            'dashboard.view',
            'homepage-sections.view', 'homepage-sections.update',
            'homepage-video.view', 'homepage-video.update',
            'about-page.view', 'about-page.update',
            'about-videos.view', 'about-videos.create', 'about-videos.update', 'about-videos.delete',
            'music-playlists.view', 'music-playlists.create', 'music-playlists.update', 'music-playlists.delete',
            'about-teams.view', 'about-teams.create', 'about-teams.update', 'about-teams.delete',
            'services.view', 'services.create', 'services.update', 'services.delete',
            'project-categories.view', 'project-categories.create', 'project-categories.update', 'project-categories.delete',
            'projects.view', 'projects.create', 'projects.update', 'projects.delete',
            'clients.view', 'clients.create', 'clients.update', 'clients.delete',
            'blogs.view', 'blogs.create', 'blogs.update', 'blogs.delete',
            'hero-banners.view', 'hero-banners.create', 'hero-banners.update', 'hero-banners.delete',
            'page-heroes.view', 'page-heroes.create', 'page-heroes.update', 'page-heroes.delete',
            'media-library.view', 'media-library.upload', 'media-library.delete',
            'analytics.view',
            'leads.view',
            'inquiry-quotation.view', 'inquiry-quotation.create', 'inquiry-quotation.edit', 'inquiry-quotation.delete',
            'iqm-user.view', 'iqm-user.create', 'iqm-user.edit', 'iqm-user.delete',
        ])->pluck('id'));
        $supportAdmin->permissions()->sync(Permission::whereIn('slug', [
            'dashboard.view',
            'contact-messages.view', 'contact-messages.read', 'contact-messages.reply', 'contact-messages.delete',
            'blog-comments.view', 'blog-comments.approve', 'blog-comments.reject', 'blog-comments.reply', 'blog-comments.delete',
            'leads.view', 'leads.update',
            'inquiry-quotation.view', 'inquiry-quotation.create', 'inquiry-quotation.edit',
            'iqm-user.view',
        ])->pluck('id'));

        Permission::whereIn('slug', ['iqm-users.view', 'iqm-users.create', 'iqm-users.edit', 'iqm-users.delete'])->delete();

        $firstUser = User::query()->oldest('id')->first();
        if ($firstUser) {
            $firstUser->update(['role_id' => $superAdmin->id, 'is_active' => true]);
        }
    }
}
