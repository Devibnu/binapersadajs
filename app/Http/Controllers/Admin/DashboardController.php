<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutTeam;
use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\ContactMessage;
use App\Models\ContactPageSetting;
use App\Models\EmailSetting;
use App\Models\HeroBanner;
use App\Models\HomepageSetting;
use App\Models\Lead;
use App\Models\PortalConversation;
use App\Models\Project;
use App\Models\Service;
use App\Models\WebsiteSetting;
use App\Models\VisitorAnalytic;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $canViewContacts = $user->canAccess('contact-messages.view');
        $canViewComments = $user->canAccess('blog-comments.view');
        $canViewAnalytics = $user->canAccess('analytics.view');
        $canViewLeads = $user->canAccess('leads.view');
        $canViewPortalQuestions = $user->canAccess('inquiry-quotation.view')
            || $user->canAccess('project-reports.view')
            || $user->canAccess('invoice-reports.view');
        $portalQuestionSummary = $this->portalQuestionSummary($user);

        $counts = [
            'services' => Service::count(),
            'projects' => Project::count(),
            'blogs' => Blog::count(),
            'hero_banners' => HeroBanner::count(),
            'contact_messages' => $canViewContacts ? ContactMessage::count() : 0,
            'unread_messages' => $canViewContacts ? ContactMessage::where('status', 'unread')->count() : 0,
            'pending_comments' => $canViewComments ? BlogComment::where('status', 'pending')->whereNull('parent_id')->count() : 0,
            'active_about_teams' => AboutTeam::where('is_active', true)->count(),
            'today_visitors' => $canViewAnalytics
                ? VisitorAnalytic::whereBetween('visited_at', [now()->startOfDay(), now()->endOfDay()])->distinct('session_id')->count('session_id')
                : 0,
            'month_visitors' => $canViewAnalytics
                ? VisitorAnalytic::whereBetween('visited_at', [now()->startOfMonth(), now()->endOfMonth()])->distinct('session_id')->count('session_id')
                : 0,
            'total_leads' => $canViewLeads ? Lead::count() : 0,
            'new_leads' => $canViewLeads ? Lead::where('status', 'new')->count() : 0,
            'month_leads' => $canViewLeads ? Lead::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count() : 0,
            'portal_questions' => $canViewPortalQuestions ? $portalQuestionSummary['total'] : 0,
        ];

        $summaryCards = [
            ['key' => 'services', 'label' => 'Total Services', 'value' => $counts['services'], 'icon' => 'fa-tools', 'color' => 'bg-gradient-primary'],
            ['key' => 'projects', 'label' => 'Total Projects', 'value' => $counts['projects'], 'icon' => 'fa-building', 'color' => 'bg-gradient-info'],
            ['key' => 'blogs', 'label' => 'Total Blogs', 'value' => $counts['blogs'], 'icon' => 'fa-newspaper', 'color' => 'bg-gradient-success'],
            ['key' => 'hero_banners', 'label' => 'Total Hero Banner', 'value' => $counts['hero_banners'], 'icon' => 'fa-images', 'color' => 'bg-gradient-secondary'],
            ['key' => 'contact_messages', 'label' => 'Pesan Kontak Masuk', 'value' => $counts['contact_messages'], 'icon' => 'fa-envelope', 'color' => 'bg-gradient-dark'],
            ['key' => 'unread_messages', 'label' => 'Pesan Belum Dibaca', 'value' => $counts['unread_messages'], 'icon' => 'fa-envelope-open-text', 'color' => 'bg-gradient-warning'],
            ['key' => 'pending_comments', 'label' => 'Komentar Pending', 'value' => $counts['pending_comments'], 'icon' => 'fa-comments', 'color' => 'bg-gradient-danger'],
            ['key' => 'active_about_teams', 'label' => 'About Teams Aktif', 'value' => $counts['active_about_teams'], 'icon' => 'fa-users', 'color' => 'bg-gradient-success'],
        ];

        if ($canViewAnalytics) {
            $summaryCards[] = ['key' => 'today_visitors', 'label' => 'Visitor Hari Ini', 'value' => $counts['today_visitors'], 'icon' => 'fa-chart-line', 'color' => 'bg-gradient-info'];
            $summaryCards[] = ['key' => 'month_visitors', 'label' => 'Visitor Bulan Ini', 'value' => $counts['month_visitors'], 'icon' => 'fa-calendar-alt', 'color' => 'bg-gradient-primary'];
        }

        if ($canViewLeads) {
            $summaryCards[] = ['key' => 'total_leads', 'label' => 'Total Leads', 'value' => $counts['total_leads'], 'icon' => 'fa-user-plus', 'color' => 'bg-gradient-success'];
            $summaryCards[] = ['key' => 'new_leads', 'label' => 'Leads Baru', 'value' => $counts['new_leads'], 'icon' => 'fa-bullseye', 'color' => 'bg-gradient-warning'];
            $summaryCards[] = ['key' => 'month_leads', 'label' => 'Leads Bulan Ini', 'value' => $counts['month_leads'], 'icon' => 'fa-calendar-check', 'color' => 'bg-gradient-info'];
        }

        if ($canViewPortalQuestions) {
            $summaryCards[] = ['key' => 'portal_questions', 'label' => 'Pertanyaan IQM Baru', 'value' => $counts['portal_questions'], 'icon' => 'fa-message', 'color' => 'bg-gradient-warning'];
        }

        $latestContactMessages = $canViewContacts ? ContactMessage::latest()->limit(5)->get() : collect();
        $pendingComments = $canViewComments
            ? BlogComment::with('blog')->where('status', 'pending')->whereNull('parent_id')->latest()->limit(5)->get()
            : collect();
        $latestBlogs = Blog::orderByDesc('published_at')->latest()->limit(5)->get();

        $websiteSetting = WebsiteSetting::query()->first();
        $setupChecklist = [
            $this->checklistItem(
                'Website Settings lengkap',
                $websiteSetting
                    && filled($websiteSetting->nama_perusahaan)
                    && filled($websiteSetting->email)
                    && filled($websiteSetting->telepon)
                    && filled($websiteSetting->alamat)
            ),
            $this->checklistItem('SMTP aktif', EmailSetting::where('is_active', true)->exists()),
            $this->checklistItem('WhatsApp tersedia', $websiteSetting && filled($websiteSetting->whatsapp)),
            $this->checklistItem('Contact Page tersedia', ContactPageSetting::query()->exists()),
            $this->checklistItem('Homepage Sections tersedia', HomepageSetting::query()->exists()),
        ];

        $quickActions = collect([
            ['permission' => 'services.create', 'label' => 'Tambah Service', 'icon' => 'fa-plus-circle', 'url' => route('paneladmin.services.create')],
            ['permission' => 'projects.create', 'label' => 'Tambah Project', 'icon' => 'fa-plus-circle', 'url' => route('paneladmin.projects.create')],
            ['permission' => 'blogs.create', 'label' => 'Tambah Blog', 'icon' => 'fa-plus-circle', 'url' => route('paneladmin.blogs.create')],
            ['permission' => 'hero-banners.create', 'label' => 'Tambah Hero Banner', 'icon' => 'fa-plus-circle', 'url' => route('paneladmin.hero-banners.create')],
            ['permission' => 'contact-messages.view', 'label' => 'Contact Inbox', 'icon' => 'fa-inbox', 'url' => route('paneladmin.contact-messages.index')],
            ['permission' => 'homepage-sections.view', 'label' => 'Homepage Sections', 'icon' => 'fa-home', 'url' => route('paneladmin.homepage-sections.edit')],
            ['permission' => 'about-page.view', 'label' => 'About Page', 'icon' => 'fa-address-card', 'url' => route('paneladmin.about-page.edit')],
            ['permission' => 'email-settings.view', 'label' => 'Email Settings', 'icon' => 'fa-paper-plane', 'url' => route('paneladmin.email-settings.edit')],
            ['permission' => 'analytics.view', 'label' => 'Analytics Visitor', 'icon' => 'fa-chart-line', 'url' => route('paneladmin.analytics.index')],
            ['permission' => 'leads.view', 'label' => 'Leads', 'icon' => 'fa-user-plus', 'url' => route('paneladmin.leads.index')],
        ])->filter(fn ($action) => $user->canAccess($action['permission']))->values()->all();

        return view('dashboard', compact(
            'counts',
            'summaryCards',
            'latestContactMessages',
            'pendingComments',
            'latestBlogs',
            'setupChecklist',
            'quickActions',
            'portalQuestionSummary',
            'canViewPortalQuestions'
        ));
    }

    private function portalQuestionSummary($user): array
    {
        $modules = collect([
            PortalConversation::MODULE_INQUIRY => [
                'label' => 'Inquiry',
                'permission' => 'inquiry-quotation.view',
            ],
            PortalConversation::MODULE_PROJECT_REPORT => [
                'label' => 'Project Report',
                'permission' => 'project-reports.view',
            ],
            PortalConversation::MODULE_INVOICE_REPORT => [
                'label' => 'Invoice Report',
                'permission' => 'invoice-reports.view',
            ],
        ])->filter(fn (array $module) => $user->canAccess($module['permission']));

        $items = $modules->map(function (array $module, string $moduleType) {
            $query = PortalConversation::query()
                ->where('module_type', $moduleType)
                ->where('sender_type', 'client')
                ->where('is_read', false);

            return [
                'label' => $module['label'],
                'count' => (clone $query)->count(),
                'latest_at' => (clone $query)->latest('created_at')->value('created_at'),
            ];
        })->values();

        return [
            'total' => $items->sum('count'),
            'latest_at' => $items->pluck('latest_at')->filter()->max(),
            'items' => $items,
        ];
    }

    private function checklistItem(string $label, bool $complete): array
    {
        return [
            'label' => $label,
            'complete' => $complete,
            'status' => $complete ? 'Lengkap' : 'Perlu dilengkapi',
            'badge' => $complete ? 'bg-gradient-success' : 'bg-gradient-warning',
        ];
    }
}
