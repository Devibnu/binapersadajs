<?php

namespace App\Providers;

use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $setting = null;

        try {
            if (Schema::hasTable('website_settings')) {
                $setting = WebsiteSetting::first();
            }
        } catch (\Exception $e) {
            $setting = null;
        }

        View::share('websiteSetting', $setting);
    }
}
