<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Directive untuk WebP image URL
        Blade::directive('webpImg', function ($path) {
            return "<?php echo \App\Helpers\ImageHelper::webpUrl({$path}); ?>";
        });

        // Directive untuk picture element dengan WebP
        Blade::directive('picture', function ($arguments) {
            return "<?php echo \App\Helpers\ImageHelper::picture({$arguments}); ?>";
        });

        // Directive untuk background image dengan WebP
        Blade::directive('bgImg', function ($path) {
            return "<?php echo \App\Helpers\ImageHelper::backgroundImage({$path}); ?>";
        });

        // Share WebP detection helper
        view()->share('supportsWebp', function ($path) {
            $pathInfo = pathinfo($path);
            $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
            return file_exists(public_path($webpPath));
        });
    }
}
