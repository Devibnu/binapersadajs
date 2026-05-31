<?php

namespace App\Services;

use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PwaIconGenerator
{
    private const ICON_SIZES = [72, 96, 128, 144, 152, 180, 192, 384, 512];
    private const BRAND_COLOR = [12, 30, 53];

    public function generateFromWebsiteSetting(WebsiteSetting $setting): void
    {
        if (! $setting->logo || ! Storage::disk('public')->exists($setting->logo)) {
            return;
        }

        $this->generate(Storage::disk('public')->path($setting->logo));
    }

    public function generate(string $sourcePath): void
    {
        if (! extension_loaded('gd')) {
            throw new RuntimeException('GD extension is required to generate PWA icons.');
        }

        $source = $this->createImage($sourcePath);
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $iconsPath = public_path('icons');

        File::ensureDirectoryExists($iconsPath);

        foreach (self::ICON_SIZES as $size) {
            $this->writePngIcon($source, $sourceWidth, $sourceHeight, "{$iconsPath}/icon-{$size}x{$size}.png", $size);
        }

        copy("{$iconsPath}/icon-180x180.png", "{$iconsPath}/apple-touch-icon.png");
        copy("{$iconsPath}/icon-180x180.png", public_path('apple-touch-icon.png'));
        copy("{$iconsPath}/icon-192x192.png", "{$iconsPath}/android-chrome-192x192.png");
        copy("{$iconsPath}/icon-512x512.png", "{$iconsPath}/android-chrome-512x512.png");
        copy("{$iconsPath}/icon-192x192.png", "{$iconsPath}/maskable-icon-192x192.png");
        copy("{$iconsPath}/icon-512x512.png", "{$iconsPath}/maskable-icon-512x512.png");

        $this->writePngIcon($source, $sourceWidth, $sourceHeight, "{$iconsPath}/favicon-16x16.png", 16);
        $this->writePngIcon($source, $sourceWidth, $sourceHeight, "{$iconsPath}/favicon-32x32.png", 32);
        copy("{$iconsPath}/favicon-32x32.png", public_path('favicon-32x32.png'));
        $this->writeIco("{$iconsPath}/favicon-32x32.png", public_path('favicon.ico'));
        $this->touchVersionFile();
        $this->updateServiceWorkerCacheVersion();

        imagedestroy($source);
    }

    private function createImage(string $path): \GdImage
    {
        $type = @exif_imagetype($path);

        return match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_WEBP => imagecreatefromwebp($path),
            default => throw new RuntimeException('Unsupported logo image format for PWA icon generation.'),
        };
    }

    private function writePngIcon(\GdImage $source, int $sourceWidth, int $sourceHeight, string $path, int $size): void
    {
        $canvas = imagecreatetruecolor($size, $size);
        $background = imagecolorallocate($canvas, ...self::BRAND_COLOR);

        imagefilledrectangle($canvas, 0, 0, $size, $size, $background);

        $padding = (int) max(2, round($size * 0.12));
        $maxSize = $size - ($padding * 2);
        $scale = min($maxSize / $sourceWidth, $maxSize / $sourceHeight);
        $width = (int) round($sourceWidth * $scale);
        $height = (int) round($sourceHeight * $scale);
        $x = (int) (($size - $width) / 2);
        $y = (int) (($size - $height) / 2);

        imagecopyresampled($canvas, $source, $x, $y, 0, 0, $width, $height, $sourceWidth, $sourceHeight);
        imagepng($canvas, $path, 9);
        imagedestroy($canvas);
    }

    private function writeIco(string $pngPath, string $icoPath): void
    {
        $png = file_get_contents($pngPath);
        $header = pack('vvv', 0, 1, 1);
        $directory = pack('CCCCvvVV', 32, 32, 0, 0, 1, 32, strlen($png), 22);

        file_put_contents($icoPath, $header . $directory . $png);
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
            "/const CACHE_NAME = 'bpjs-pwa-v[^']+';/",
            "const CACHE_NAME = 'bpjs-pwa-v{$version}';",
            $serviceWorker,
            1
        );

        File::put($serviceWorkerPath, $serviceWorker);
    }
}
