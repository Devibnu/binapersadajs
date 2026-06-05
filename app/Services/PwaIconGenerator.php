<?php

namespace App\Services;

use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PwaIconGenerator
{
    private const ICON_SIZES = [72, 96, 128, 144, 152, 180, 192, 384, 512];
    private const BRAND_COLOR = [12, 30, 53];

    public function generateFromWebsiteSetting(WebsiteSetting $setting): void
    {
        if (! $setting->logo || ! Storage::disk('public')->exists($setting->logo)) {
            Log::info('Skipping PWA icon generation because website logo is empty or missing.', [
                'logo' => $setting->logo,
            ]);

            return;
        }

        $this->generate(Storage::disk('public')->path($setting->logo));
    }

    public function generate(string $sourcePath): void
    {
        if (! extension_loaded('gd')) {
            throw new RuntimeException('GD extension is required to generate PWA icons.');
        }

        if (! is_file($sourcePath)) {
            throw new RuntimeException("PWA icon source logo not found: {$sourcePath}");
        }

        $source = $this->createImage($sourcePath);
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $iconsPath = public_path('icons');
        $tempPath = "{$iconsPath}/.tmp-pwa-icons-" . uniqid('', true);

        File::ensureDirectoryExists($iconsPath);
        File::ensureDirectoryExists($tempPath);

        try {
            foreach (self::ICON_SIZES as $size) {
                $this->writePngIcon($source, $sourceWidth, $sourceHeight, "{$tempPath}/icon-{$size}x{$size}.png", $size);
            }

            $this->copyGeneratedIcon("{$tempPath}/icon-180x180.png", "{$tempPath}/apple-touch-icon.png");
            $this->copyGeneratedIcon("{$tempPath}/icon-180x180.png", "{$tempPath}/root-apple-touch-icon.png");
            $this->copyGeneratedIcon("{$tempPath}/icon-192x192.png", "{$tempPath}/android-chrome-192x192.png");
            $this->copyGeneratedIcon("{$tempPath}/icon-512x512.png", "{$tempPath}/android-chrome-512x512.png");
            $this->copyGeneratedIcon("{$tempPath}/icon-192x192.png", "{$tempPath}/maskable-icon-192x192.png");
            $this->copyGeneratedIcon("{$tempPath}/icon-512x512.png", "{$tempPath}/maskable-icon-512x512.png");
            $this->copyGeneratedIcon("{$tempPath}/icon-192x192.png", "{$tempPath}/maskable-192x192.png");
            $this->copyGeneratedIcon("{$tempPath}/icon-512x512.png", "{$tempPath}/maskable-512x512.png");
            $this->copyGeneratedIcon("{$tempPath}/icon-192x192.png", "{$tempPath}/icon-192-v2.png");
            $this->copyGeneratedIcon("{$tempPath}/icon-512x512.png", "{$tempPath}/icon-512-v2.png");
            $this->copyGeneratedIcon("{$tempPath}/icon-192x192.png", "{$tempPath}/maskable-192-v2.png");
            $this->copyGeneratedIcon("{$tempPath}/icon-512x512.png", "{$tempPath}/maskable-512-v2.png");

            $this->writePngIcon($source, $sourceWidth, $sourceHeight, "{$tempPath}/favicon-16x16.png", 16);
            $this->writePngIcon($source, $sourceWidth, $sourceHeight, "{$tempPath}/favicon-32x32.png", 32);
            $this->copyGeneratedIcon("{$tempPath}/favicon-32x32.png", "{$tempPath}/root-favicon-32x32.png");
            $this->writeIco("{$tempPath}/favicon-32x32.png", "{$tempPath}/favicon.ico");

            $this->publishGeneratedIcons($tempPath, $iconsPath);
            $this->touchVersionFile();
            $this->updateServiceWorkerCacheVersion();
        } finally {
            File::deleteDirectory($tempPath);
            imagedestroy($source);
        }

        Log::info('PWA icons generated successfully.', [
            'source' => $sourcePath,
        ]);
    }

    private function createImage(string $path): \GdImage
    {
        $type = @exif_imagetype($path);

        $image = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_WEBP => imagecreatefromwebp($path),
            default => throw new RuntimeException('Unsupported logo image format for PWA icon generation.'),
        };

        if (! $image instanceof \GdImage) {
            throw new RuntimeException("Unable to read logo image for PWA icon generation: {$path}");
        }

        return $image;
    }

    private function writePngIcon(\GdImage $source, int $sourceWidth, int $sourceHeight, string $path, int $size): void
    {
        $canvas = imagecreatetruecolor($size, $size);

        if (! $canvas) {
            throw new RuntimeException("Unable to create {$size}x{$size} icon canvas.");
        }

        $background = imagecolorallocate($canvas, ...self::BRAND_COLOR);

        imagefilledrectangle($canvas, 0, 0, $size, $size, $background);

        $padding = (int) max(2, round($size * 0.12));
        $maxSize = $size - ($padding * 2);
        $scale = min($maxSize / $sourceWidth, $maxSize / $sourceHeight);
        $width = (int) round($sourceWidth * $scale);
        $height = (int) round($sourceHeight * $scale);
        $x = (int) (($size - $width) / 2);
        $y = (int) (($size - $height) / 2);

        if (! imagecopyresampled($canvas, $source, $x, $y, 0, 0, $width, $height, $sourceWidth, $sourceHeight)) {
            imagedestroy($canvas);
            throw new RuntimeException("Unable to resize PWA icon {$path}.");
        }

        if (! imagepng($canvas, $path, 9)) {
            imagedestroy($canvas);
            throw new RuntimeException("Unable to write PWA icon {$path}.");
        }

        imagedestroy($canvas);
    }

    private function writeIco(string $pngPath, string $icoPath): void
    {
        $png = file_get_contents($pngPath);

        if ($png === false) {
            throw new RuntimeException("Unable to read PNG source for ICO: {$pngPath}");
        }

        $header = pack('vvv', 0, 1, 1);
        $directory = pack('CCCCvvVV', 32, 32, 0, 0, 1, 32, strlen($png), 22);

        if (file_put_contents($icoPath, $header . $directory . $png) === false) {
            throw new RuntimeException("Unable to write ICO file: {$icoPath}");
        }
    }

    private function copyGeneratedIcon(string $from, string $to): void
    {
        if (! copy($from, $to)) {
            throw new RuntimeException("Unable to copy generated PWA icon from {$from} to {$to}.");
        }
    }

    private function publishGeneratedIcons(string $tempPath, string $iconsPath): void
    {
        $files = [
            'icon-72x72.png' => "{$iconsPath}/icon-72x72.png",
            'icon-96x96.png' => "{$iconsPath}/icon-96x96.png",
            'icon-128x128.png' => "{$iconsPath}/icon-128x128.png",
            'icon-144x144.png' => "{$iconsPath}/icon-144x144.png",
            'icon-152x152.png' => "{$iconsPath}/icon-152x152.png",
            'icon-180x180.png' => "{$iconsPath}/icon-180x180.png",
            'icon-192x192.png' => "{$iconsPath}/icon-192x192.png",
            'icon-384x384.png' => "{$iconsPath}/icon-384x384.png",
            'icon-512x512.png' => "{$iconsPath}/icon-512x512.png",
            'apple-touch-icon.png' => "{$iconsPath}/apple-touch-icon.png",
            'root-apple-touch-icon.png' => public_path('apple-touch-icon.png'),
            'android-chrome-192x192.png' => "{$iconsPath}/android-chrome-192x192.png",
            'android-chrome-512x512.png' => "{$iconsPath}/android-chrome-512x512.png",
            'maskable-icon-192x192.png' => "{$iconsPath}/maskable-icon-192x192.png",
            'maskable-icon-512x512.png' => "{$iconsPath}/maskable-icon-512x512.png",
            'maskable-192x192.png' => "{$iconsPath}/maskable-192x192.png",
            'maskable-512x512.png' => "{$iconsPath}/maskable-512x512.png",
            'icon-192-v2.png' => "{$iconsPath}/icon-192-v2.png",
            'icon-512-v2.png' => "{$iconsPath}/icon-512-v2.png",
            'maskable-192-v2.png' => "{$iconsPath}/maskable-192-v2.png",
            'maskable-512-v2.png' => "{$iconsPath}/maskable-512-v2.png",
            'favicon-16x16.png' => "{$iconsPath}/favicon-16x16.png",
            'favicon-32x32.png' => "{$iconsPath}/favicon-32x32.png",
            'root-favicon-32x32.png' => public_path('favicon-32x32.png'),
            'favicon.ico' => public_path('favicon.ico'),
        ];

        foreach ($files as $source => $target) {
            $sourcePath = "{$tempPath}/{$source}";

            if (dirname($target) === $iconsPath) {
                File::replace($target, File::get($sourcePath));
                continue;
            }

            $this->copyGeneratedIcon($sourcePath, $target);
        }
    }

    private function touchVersionFile(): void
    {
        file_put_contents(public_path('icons/pwa-version.txt'), (string) time());
    }

    private function updateServiceWorkerCacheVersion(): void
    {
        $serviceWorkerPath = public_path('sw.js');

        if (! File::exists($serviceWorkerPath)) {
            return;
        }

        $version = time();
        $serviceWorker = File::get($serviceWorkerPath);
        $serviceWorker = preg_replace(
            "/const CACHE_NAME = 'binapersadajs-v[^']+';/",
            "const CACHE_NAME = 'binapersadajs-v{$version}';",
            $serviceWorker,
            1
        );

        File::put($serviceWorkerPath, $serviceWorker);
    }
}
