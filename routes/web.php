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
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\WebsiteBlogController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WebsiteController::class, 'home'])->name('website.home');
Route::get('/about', [WebsiteController::class, 'about'])->name('website.about');
Route::get('/services', [FrontendServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [FrontendServiceController::class, 'show'])->name('services.show');
Route::redirect('/service-single', '/services')->name('website.service-single');
Route::get('/projects', [FrontendProjectController::class, 'index'])->name('website.projects');
Route::get('/projects/{slug}', [FrontendProjectController::class, 'show'])->name('projects.show');
Route::get('/contact', [WebsiteController::class, 'contact'])->name('website.contact');
Route::get('/blog', [WebsiteBlogController::class, 'index'])->name('website.blog.index');
Route::get('/blog/{slug}', [WebsiteBlogController::class, 'show'])->name('website.blog.show');
Route::view('/offline', 'website.offline')->name('website.offline');

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
        Route::get('/', function () {
            return view('dashboard');
        })->name('paneladmin.dashboard');

        Route::post('/logout', [SessionsController::class, 'destroy'])->name('paneladmin.logout');
        Route::get('/settings', [WebsiteSettingController::class, 'edit'])->name('paneladmin.settings.edit');
        Route::put('/settings', [WebsiteSettingController::class, 'update'])->name('paneladmin.settings.update');
        Route::resource('/hero-banners', HeroBannerController::class)
            ->except('show')
            ->names('paneladmin.hero-banners');
        Route::resource('/services', ServiceController::class)
            ->except('show')
            ->names('paneladmin.services');
        Route::resource('/projects', ProjectController::class)
            ->names('paneladmin.projects');
        Route::resource('/project-categories', ProjectCategoryController::class)
            ->except('show')
            ->names('paneladmin.project-categories');
        Route::resource('/page-heroes', PageHeroController::class)
            ->except('show')
            ->names('paneladmin.page-heroes');
        Route::get('/user-profile', [InfoUserController::class, 'create'])->name('paneladmin.user-profile');
        Route::post('/user-profile', [InfoUserController::class, 'store'])->name('paneladmin.user-profile.store');

        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('paneladmin.dashboard.alias');
        Route::get('/billing', function () {
            return view('billing');
        })->name('paneladmin.billing');
        Route::get('/profile', function () {
            return view('profile');
        })->name('paneladmin.profile');
        Route::get('/rtl', function () {
            return view('rtl');
        })->name('paneladmin.rtl');
        Route::get('/user-management', function () {
            return view('laravel-examples/user-management');
        })->name('paneladmin.user-management');
        Route::get('/tables', function () {
            return view('tables');
        })->name('paneladmin.tables');
        Route::get('/virtual-reality', function () {
            return view('virtual-reality');
        })->name('paneladmin.virtual-reality');
        Route::get('/static-sign-in', function () {
            return view('static-sign-in');
        })->name('paneladmin.sign-in');
        Route::get('/static-sign-up', function () {
            return view('static-sign-up');
        })->name('paneladmin.sign-up');
    });
});

Route::get('/login', function () {
    return redirect()->route('paneladmin.login');
})->name('login');

Route::get('/logout', [SessionsController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
