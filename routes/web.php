<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\FrontendServiceController;
use App\Http\Controllers\FrontendProjectController;
use App\Http\Controllers\Admin\HeroBannerController;
use App\Http\Controllers\Admin\PageHeroController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\WebsiteSettingController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BlogCommentController as AdminBlogCommentController;
use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\ContactPageSettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailSettingController;
use App\Http\Controllers\Admin\EditorImageController;
use App\Http\Controllers\Admin\HomepageSettingController;
use App\Http\Controllers\Admin\HomepageVideoController;
use App\Http\Controllers\Admin\AboutPageSettingController;
use App\Http\Controllers\Admin\AboutTeamController;
use App\Http\Controllers\Admin\SeoSettingController;
use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\LeadController as AdminLeadController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\InquiryQuotationController;
use App\Http\Controllers\Admin\IqmUserController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\BlogCommentController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\IqmPortalController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\WebsiteBlogController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

Route::get('/favicon.ico', function () {
    $faviconVersion = file_exists(public_path('icons/pwa-version.txt'))
        ? trim(file_get_contents(public_path('icons/pwa-version.txt')))
        : filemtime(public_path('icons/favicon-32x32.png'));

    return redirect()->to(asset('icons/favicon-32x32.png') . '?v=' . $faviconVersion);
});

Route::get('/', [WebsiteController::class, 'home'])->middleware('analytics.track')->name('website.home');
Route::get('/about', [WebsiteController::class, 'about'])->middleware('analytics.track')->name('website.about');
Route::get('/services', [FrontendServiceController::class, 'index'])->middleware('analytics.track')->name('services.index');
Route::get('/services/{slug}', [FrontendServiceController::class, 'show'])->middleware('analytics.track')->name('services.show');
Route::redirect('/service-single', '/services')->name('website.service-single');
Route::get('/projects', [FrontendProjectController::class, 'index'])->middleware('analytics.track')->name('website.projects');
Route::get('/projects/{slug}', [FrontendProjectController::class, 'show'])->name('projects.show');
Route::get('/contact', [WebsiteController::class, 'contact'])->middleware('analytics.track')->name('website.contact');
Route::post('/contact', [ContactMessageController::class, 'store'])
    ->middleware('throttle:5,10')
    ->name('website.contact.store');
Route::get('/blog', [WebsiteBlogController::class, 'index'])->middleware('analytics.track')->name('website.blog.index');
Route::get('/blog/{slug}', [WebsiteBlogController::class, 'show'])->middleware('analytics.track')->name('website.blog.show');
Route::post('/blog/{blog:slug}/comments', [BlogCommentController::class, 'store'])
    ->middleware('throttle:3,60')
    ->name('website.blog.comments.store');
Route::post('/leads/newsletter', [LeadController::class, 'storeNewsletter'])
    ->middleware('throttle:5,10')
    ->name('website.leads.newsletter');
Route::post('/leads/inquiry', [LeadController::class, 'storeInquiry'])
    ->middleware('throttle:5,10')
    ->name('website.leads.inquiry');
Route::view('/offline', 'website.offline')->name('website.offline');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('website.sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('website.robots');

Route::prefix('iqm')->name('iqm.')->group(function () {
    Route::get('/', [IqmPortalController::class, 'dashboard'])->middleware('auth:iqm')->name('landing');
    Route::middleware('guest:iqm')->group(function () {
        Route::get('/login', [IqmPortalController::class, 'login'])->name('login');
        Route::post('/login', [IqmPortalController::class, 'authenticate'])->name('authenticate');
    });
    Route::middleware('auth:iqm')->group(function () {
        Route::post('/logout', [IqmPortalController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [IqmPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/inquiries', [IqmPortalController::class, 'inquiries'])->name('inquiries.index');
        Route::get('/quotations', [IqmPortalController::class, 'quotations'])->name('quotations.index');
        Route::get('/attachments', [IqmPortalController::class, 'attachments'])->name('attachments.index');
        Route::get('/profile', [IqmPortalController::class, 'profile'])->name('profile');
        Route::get('/inquiries/{inquiryQuotation}', [IqmPortalController::class, 'show'])->name('inquiries.show');
    });
});

Route::prefix('paneladmin')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [SessionsController::class, 'create'])->name('paneladmin.login');
        Route::post('/session', [SessionsController::class, 'store'])->name('paneladmin.session');
        Route::get('/login/forgot-password', [ResetController::class, 'create'])->name('paneladmin.password.request');
        Route::post('/forgot-password', [ResetController::class, 'sendEmail'])->name('paneladmin.password.email');
        Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('paneladmin.password.reset');
        Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('paneladmin.password.update');
    });

    Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name('paneladmin.dashboard');

        Route::post('/logout', [SessionsController::class, 'destroy'])->name('paneladmin.logout');
        Route::get('/settings', [WebsiteSettingController::class, 'edit'])->middleware('permission:website-settings.view')->name('paneladmin.settings.edit');
        Route::put('/settings', [WebsiteSettingController::class, 'update'])->middleware('permission:website-settings.update')->name('paneladmin.settings.update');
        Route::get('/contact-page', [ContactPageSettingController::class, 'edit'])->middleware('permission:contact-page.view')->name('paneladmin.contact-page.edit');
        Route::put('/contact-page', [ContactPageSettingController::class, 'update'])->middleware('permission:contact-page.update')->name('paneladmin.contact-page.update');
        Route::get('/email-settings', [EmailSettingController::class, 'edit'])->middleware('permission:email-settings.view')->name('paneladmin.email-settings.edit');
        Route::put('/email-settings', [EmailSettingController::class, 'update'])->middleware('permission:email-settings.update')->name('paneladmin.email-settings.update');
        Route::post('/email-settings/test', [EmailSettingController::class, 'sendTest'])->middleware('permission:email-settings.test')->name('paneladmin.email-settings.test');
        Route::get('/homepage-sections', [HomepageSettingController::class, 'edit'])->middleware('permission:homepage-sections.view')->name('paneladmin.homepage-sections.edit');
        Route::put('/homepage-sections', [HomepageSettingController::class, 'update'])->middleware('permission:homepage-sections.update')->name('paneladmin.homepage-sections.update');
        Route::get('/homepage-video', [HomepageVideoController::class, 'index'])->middleware('permission:homepage-video.view')->name('paneladmin.homepage-video.index');
        Route::get('/homepage-video/edit', [HomepageVideoController::class, 'edit'])->middleware('permission:homepage-video.view')->name('paneladmin.homepage-video.edit');
        Route::put('/homepage-video', [HomepageVideoController::class, 'update'])->middleware('permission:homepage-video.update')->name('paneladmin.homepage-video.update');
        Route::get('/seo-settings', [SeoSettingController::class, 'edit'])->middleware('permission:seo-settings.view')->name('paneladmin.seo-settings.edit');
        Route::put('/seo-settings', [SeoSettingController::class, 'update'])->middleware('permission:seo-settings.update')->name('paneladmin.seo-settings.update');
        Route::get('/media-library', [MediaLibraryController::class, 'index'])->middleware('permission:media-library.view')->name('paneladmin.media-library.index');
        Route::get('/media-library/create', [MediaLibraryController::class, 'create'])->middleware('permission:media-library.upload')->name('paneladmin.media-library.create');
        Route::post('/media-library', [MediaLibraryController::class, 'store'])->middleware('permission:media-library.upload')->name('paneladmin.media-library.store');
        Route::get('/media-library/{mediaFile}/download', [MediaLibraryController::class, 'download'])->middleware('permission:media-library.view')->name('paneladmin.media-library.download');
        Route::get('/media-library/{mediaFile}', [MediaLibraryController::class, 'show'])->middleware('permission:media-library.view')->name('paneladmin.media-library.show');
        Route::delete('/media-library/{mediaFile}', [MediaLibraryController::class, 'destroy'])->middleware('permission:media-library.delete')->name('paneladmin.media-library.destroy');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->middleware('permission:activity-logs.view')->name('paneladmin.activity-logs.index');
        Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->middleware('permission:activity-logs.view')->name('paneladmin.activity-logs.show');
        Route::get('/analytics', [AnalyticsController::class, 'index'])->middleware('permission:analytics.view')->name('paneladmin.analytics.index');
        Route::get('/about-page', [AboutPageSettingController::class, 'edit'])->middleware('permission:about-page.view')->name('paneladmin.about-page.edit');
        Route::put('/about-page', [AboutPageSettingController::class, 'update'])->middleware('permission:about-page.update')->name('paneladmin.about-page.update');
        Route::get('/about-teams', [AboutTeamController::class, 'index'])->middleware('permission:about-teams.view')->name('paneladmin.about-teams.index');
        Route::get('/about-teams/create', [AboutTeamController::class, 'create'])->middleware('permission:about-teams.create')->name('paneladmin.about-teams.create');
        Route::post('/about-teams', [AboutTeamController::class, 'store'])->middleware('permission:about-teams.create')->name('paneladmin.about-teams.store');
        Route::get('/about-teams/{aboutTeam}/edit', [AboutTeamController::class, 'edit'])->middleware('permission:about-teams.update')->name('paneladmin.about-teams.edit');
        Route::put('/about-teams/{aboutTeam}', [AboutTeamController::class, 'update'])->middleware('permission:about-teams.update')->name('paneladmin.about-teams.update');
        Route::delete('/about-teams/{aboutTeam}', [AboutTeamController::class, 'destroy'])->middleware('permission:about-teams.delete')->name('paneladmin.about-teams.destroy');
        Route::get('/hero-banners', [HeroBannerController::class, 'index'])->middleware('permission:hero-banners.view')->name('paneladmin.hero-banners.index');
        Route::get('/hero-banners/create', [HeroBannerController::class, 'create'])->middleware('permission:hero-banners.create')->name('paneladmin.hero-banners.create');
        Route::post('/hero-banners', [HeroBannerController::class, 'store'])->middleware('permission:hero-banners.create')->name('paneladmin.hero-banners.store');
        Route::get('/hero-banners/{heroBanner}/edit', [HeroBannerController::class, 'edit'])->middleware('permission:hero-banners.update')->name('paneladmin.hero-banners.edit');
        Route::put('/hero-banners/{heroBanner}', [HeroBannerController::class, 'update'])->middleware('permission:hero-banners.update')->name('paneladmin.hero-banners.update');
        Route::delete('/hero-banners/{heroBanner}', [HeroBannerController::class, 'destroy'])->middleware('permission:hero-banners.delete')->name('paneladmin.hero-banners.destroy');
        Route::get('/services', [ServiceController::class, 'index'])->middleware('permission:services.view')->name('paneladmin.services.index');
        Route::get('/services/create', [ServiceController::class, 'create'])->middleware('permission:services.create')->name('paneladmin.services.create');
        Route::post('/services', [ServiceController::class, 'store'])->middleware('permission:services.create')->name('paneladmin.services.store');
        Route::get('/services/{service}', [ServiceController::class, 'show'])->middleware('permission:services.view')->name('paneladmin.services.show');
        Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->middleware('permission:services.update')->name('paneladmin.services.edit');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->middleware('permission:services.update')->name('paneladmin.services.update');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->middleware('permission:services.delete')->name('paneladmin.services.destroy');
        Route::get('/projects', [ProjectController::class, 'index'])->middleware('permission:projects.view')->name('paneladmin.projects.index');
        Route::get('/projects/create', [ProjectController::class, 'create'])->middleware('permission:projects.create')->name('paneladmin.projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->middleware('permission:projects.create')->name('paneladmin.projects.store');
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->middleware('permission:projects.view')->name('paneladmin.projects.show');
        Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->middleware('permission:projects.update')->name('paneladmin.projects.edit');
        Route::put('/projects/{project}', [ProjectController::class, 'update'])->middleware('permission:projects.update')->name('paneladmin.projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->middleware('permission:projects.delete')->name('paneladmin.projects.destroy');
        Route::get('/clients', [ClientController::class, 'index'])->middleware('permission:clients.view')->name('paneladmin.clients.index');
        Route::get('/clients/create', [ClientController::class, 'create'])->middleware('permission:clients.create')->name('paneladmin.clients.create');
        Route::post('/clients', [ClientController::class, 'store'])->middleware('permission:clients.create')->name('paneladmin.clients.store');
        Route::get('/clients/{client}', [ClientController::class, 'show'])->middleware('permission:clients.view')->name('paneladmin.clients.show');
        Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->middleware('permission:clients.update')->name('paneladmin.clients.edit');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->middleware('permission:clients.update')->name('paneladmin.clients.update');
        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->middleware('permission:clients.delete')->name('paneladmin.clients.destroy');
        Route::post('/editor/upload-image', [EditorImageController::class, 'store'])
            ->middleware('permission:services.update')
            ->name('paneladmin.editor.upload-image');
        Route::post('/blogs/upload-image', [BlogController::class, 'uploadImage'])
            ->middleware('permission:blogs.update')
            ->name('paneladmin.blogs.upload-image');
        Route::get('/blogs', [BlogController::class, 'index'])->middleware('permission:blogs.view')->name('paneladmin.blogs.index');
        Route::get('/blogs/create', [BlogController::class, 'create'])->middleware('permission:blogs.create')->name('paneladmin.blogs.create');
        Route::post('/blogs', [BlogController::class, 'store'])->middleware('permission:blogs.create')->name('paneladmin.blogs.store');
        Route::get('/blogs/{blog}/edit', [BlogController::class, 'edit'])->middleware('permission:blogs.update')->name('paneladmin.blogs.edit');
        Route::put('/blogs/{blog}', [BlogController::class, 'update'])->middleware('permission:blogs.update')->name('paneladmin.blogs.update');
        Route::delete('/blogs/{blog}', [BlogController::class, 'destroy'])->middleware('permission:blogs.delete')->name('paneladmin.blogs.destroy');
        Route::get('/blog-comments', [AdminBlogCommentController::class, 'index'])
            ->middleware('permission:blog-comments.view')
            ->name('paneladmin.blog-comments.index');
        Route::get('/blog-comments/{blogComment}', [AdminBlogCommentController::class, 'show'])
            ->middleware('permission:blog-comments.view')
            ->name('paneladmin.blog-comments.show');
        Route::patch('/blog-comments/{blogComment}/approve', [AdminBlogCommentController::class, 'approve'])
            ->middleware('permission:blog-comments.approve')
            ->name('paneladmin.blog-comments.approve');
        Route::patch('/blog-comments/{blogComment}/reject', [AdminBlogCommentController::class, 'reject'])
            ->middleware('permission:blog-comments.reject')
            ->name('paneladmin.blog-comments.reject');
        Route::post('/blog-comments/{blogComment}/reply', [AdminBlogCommentController::class, 'reply'])
            ->middleware('permission:blog-comments.reply')
            ->name('paneladmin.blog-comments.reply');
        Route::delete('/blog-comments/{blogComment}', [AdminBlogCommentController::class, 'destroy'])
            ->middleware('permission:blog-comments.delete')
            ->name('paneladmin.blog-comments.destroy');
        Route::get('/contact-messages', [AdminContactMessageController::class, 'index'])
            ->middleware('permission:contact-messages.view')
            ->name('paneladmin.contact-messages.index');
        Route::get('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'show'])
            ->middleware('permission:contact-messages.view')
            ->name('paneladmin.contact-messages.show');
        Route::patch('/contact-messages/{contactMessage}/read', [AdminContactMessageController::class, 'markRead'])
            ->middleware('permission:contact-messages.read')
            ->name('paneladmin.contact-messages.read');
        Route::patch('/contact-messages/{contactMessage}/replied', [AdminContactMessageController::class, 'markReplied'])
            ->middleware('permission:contact-messages.reply')
            ->name('paneladmin.contact-messages.replied');
        Route::post('/contact-messages/{contactMessage}/reply', [AdminContactMessageController::class, 'sendReply'])
            ->middleware('permission:contact-messages.reply')
            ->name('paneladmin.contact-messages.reply');
        Route::delete('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'destroy'])
            ->middleware('permission:contact-messages.delete')
            ->name('paneladmin.contact-messages.destroy');
        Route::get('/leads', [AdminLeadController::class, 'index'])
            ->middleware('permission:leads.view')
            ->name('paneladmin.leads.index');
        Route::get('/leads/{lead}', [AdminLeadController::class, 'show'])
            ->middleware('permission:leads.view')
            ->name('paneladmin.leads.show');
        Route::patch('/leads/{lead}/status', [AdminLeadController::class, 'updateStatus'])
            ->middleware('permission:leads.update')
            ->name('paneladmin.leads.status');
        Route::delete('/leads/{lead}', [AdminLeadController::class, 'destroy'])
            ->middleware('permission:leads.delete')
            ->name('paneladmin.leads.destroy');
        Route::get('/project-categories', [ProjectCategoryController::class, 'index'])->middleware('permission:project-categories.view')->name('paneladmin.project-categories.index');
        Route::get('/project-categories/create', [ProjectCategoryController::class, 'create'])->middleware('permission:project-categories.create')->name('paneladmin.project-categories.create');
        Route::post('/project-categories', [ProjectCategoryController::class, 'store'])->middleware('permission:project-categories.create')->name('paneladmin.project-categories.store');
        Route::get('/project-categories/{projectCategory}/edit', [ProjectCategoryController::class, 'edit'])->middleware('permission:project-categories.update')->name('paneladmin.project-categories.edit');
        Route::put('/project-categories/{projectCategory}', [ProjectCategoryController::class, 'update'])->middleware('permission:project-categories.update')->name('paneladmin.project-categories.update');
        Route::delete('/project-categories/{projectCategory}', [ProjectCategoryController::class, 'destroy'])->middleware('permission:project-categories.delete')->name('paneladmin.project-categories.destroy');
        Route::get('/page-heroes', [PageHeroController::class, 'index'])->middleware('permission:page-heroes.view')->name('paneladmin.page-heroes.index');
        Route::get('/page-heroes/create', [PageHeroController::class, 'create'])->middleware('permission:page-heroes.create')->name('paneladmin.page-heroes.create');
        Route::post('/page-heroes', [PageHeroController::class, 'store'])->middleware('permission:page-heroes.create')->name('paneladmin.page-heroes.store');
        Route::get('/page-heroes/{pageHero}/edit', [PageHeroController::class, 'edit'])->middleware('permission:page-heroes.update')->name('paneladmin.page-heroes.edit');
        Route::put('/page-heroes/{pageHero}', [PageHeroController::class, 'update'])->middleware('permission:page-heroes.update')->name('paneladmin.page-heroes.update');
        Route::delete('/page-heroes/{pageHero}', [PageHeroController::class, 'destroy'])->middleware('permission:page-heroes.delete')->name('paneladmin.page-heroes.destroy');
        Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:roles.view')->name('paneladmin.roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->middleware('permission:roles.create')->name('paneladmin.roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:roles.create')->name('paneladmin.roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->middleware('permission:roles.update')->name('paneladmin.roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('permission:roles.update')->name('paneladmin.roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:roles.delete')->name('paneladmin.roles.destroy');
        Route::get('/users', [UserController::class, 'index'])->middleware('permission:users.view')->name('paneladmin.users.index');
        Route::get('/users/create', [UserController::class, 'create'])->middleware('permission:users.create')->name('paneladmin.users.create');
        Route::post('/users', [UserController::class, 'store'])->middleware('permission:users.create')->name('paneladmin.users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->middleware('permission:users.update')->name('paneladmin.users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:users.update')->name('paneladmin.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('paneladmin.users.destroy');
        Route::get('/iqm-users', [IqmUserController::class, 'index'])->middleware('permission:iqm-user.view')->name('paneladmin.iqm-users.index');
        Route::get('/iqm-users/create', [IqmUserController::class, 'create'])->middleware('permission:iqm-user.create')->name('paneladmin.iqm-users.create');
        Route::post('/iqm-users', [IqmUserController::class, 'store'])->middleware('permission:iqm-user.create')->name('paneladmin.iqm-users.store');
        Route::get('/iqm-users/{iqmUser}/edit', [IqmUserController::class, 'edit'])->middleware('permission:iqm-user.edit')->name('paneladmin.iqm-users.edit');
        Route::put('/iqm-users/{iqmUser}', [IqmUserController::class, 'update'])->middleware('permission:iqm-user.edit')->name('paneladmin.iqm-users.update');
        Route::get('/iqm-users/{iqmUser}', [IqmUserController::class, 'show'])->middleware('permission:iqm-user.view')->name('paneladmin.iqm-users.show');
        Route::delete('/iqm-users/{iqmUser}', [IqmUserController::class, 'destroy'])->middleware('permission:iqm-user.delete')->name('paneladmin.iqm-users.destroy');

        // Inquiry & Quotation Management
        Route::get('/inquiries', [InquiryQuotationController::class, 'index'])->middleware('permission:inquiry-quotation.view')->name('admin.inquiries.index');
        Route::get('/inquiries/create', [InquiryQuotationController::class, 'create'])->middleware('permission:inquiry-quotation.create')->name('admin.inquiries.create');
        Route::post('/inquiries', [InquiryQuotationController::class, 'store'])->middleware('permission:inquiry-quotation.create')->name('admin.inquiries.store');
        Route::get('/inquiries/{inquiryQuotation}', [InquiryQuotationController::class, 'show'])->middleware('permission:inquiry-quotation.view')->name('admin.inquiries.show');
        Route::get('/inquiries/{inquiryQuotation}/edit', [InquiryQuotationController::class, 'edit'])->middleware('permission:inquiry-quotation.edit')->name('admin.inquiries.edit');
        Route::put('/inquiries/{inquiryQuotation}', [InquiryQuotationController::class, 'update'])->middleware('permission:inquiry-quotation.edit')->name('admin.inquiries.update');
        Route::delete('/inquiries/{inquiryQuotation}', [InquiryQuotationController::class, 'destroy'])->middleware('permission:inquiry-quotation.delete')->name('admin.inquiries.destroy');
        Route::delete('/inquiry-attachments/{attachment}', [InquiryQuotationController::class, 'deleteAttachment'])->middleware('permission:inquiry-quotation.edit')->name('admin.inquiry-attachments.delete');
        Route::get('/inquiry-attachments/{attachment}/download', [InquiryQuotationController::class, 'downloadAttachment'])->middleware('permission:inquiry-quotation.view')->name('admin.inquiry-attachments.download');

        // New resource routes for Inquiry & Quotation (inquiry-quotations)
        Route::resource('inquiry-quotations', InquiryQuotationController::class, [
            'names' => 'paneladmin.inquiry-quotations',
            'parameters' => ['inquiry-quotations' => 'inquiryQuotation']
        ]);

        Route::get('/profile', [ProfileController::class, 'show'])->name('paneladmin.profile');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('paneladmin.profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('paneladmin.profile.update');
        Route::get('/profile/password', [ProfileController::class, 'password'])->name('paneladmin.profile.password');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('paneladmin.profile.password.update');
        Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name('paneladmin.dashboard.alias');
    });
});

Route::get('/login', function () {
    return redirect()->route('paneladmin.login');
})->name('login');

Route::get('/logout', [SessionsController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
