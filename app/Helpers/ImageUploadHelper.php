<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageUploadHelper
{
    public static function uploadAndCompress(
        UploadedFile $file,
        string $folder,
        int $maxWidth = 1600,
        int $quality = 80
    ): string {
        $path = trim($folder, '/') . '/' . Str::uuid() . '.webp';
        $disk = Storage::disk('public');

        $disk->makeDirectory(trim($folder, '/'));

        Image::read($file)
            ->scaleDown(width: $maxWidth)
            ->toWebp(quality: $quality)
            ->save($disk->path($path));

        return $path;
    }

    public static function deleteStoredImage(?string $path): void
    {
        if (! $path || self::isPublicAsset($path)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private static function isPublicAsset(string $path): bool
    {
        return str_starts_with($path, 'web/')
            || str_starts_with($path, 'assets/')
            || str_starts_with($path, '/');
    }
}
