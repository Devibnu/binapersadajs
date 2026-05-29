<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Get WebP image URL with fallback to original format
     * 
     * @param string $path Image path relative to public (e.g., 'web/images/hero.jpg')
     * @param string $fallback Optional fallback format (jpg, png). If null, uses original extension
     * @return string Image URL
     */
    public static function webpUrl(string $path, ?string $fallback = null): string
    {
        $pathInfo = pathinfo($path);
        $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
        $publicPath = public_path($webpPath);

        // If WebP exists, return WebP URL
        if (file_exists($publicPath)) {
            return asset($webpPath);
        }

        // Fall back to original image
        return asset($path);
    }

    /**
     * Generate picture element HTML for responsive WebP images
     * 
     * @param string $path Image path relative to public
     * @param string $alt Alt text
     * @param array $attributes Additional HTML attributes
     * @return string HTML picture element
     */
    public static function picture(string $path, string $alt, array $attributes = []): string
    {
        $pathInfo = pathinfo($path);
        $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
        $publicPath = public_path($webpPath);
        $hasWebp = file_exists($publicPath);

        // Build attributes string
        $attrString = '';
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $attrString .= " {$key}";
            } else if ($value !== false && $value !== null) {
                $attrString .= " {$key}=\"{$value}\"";
            }
        }

        if ($hasWebp) {
            return sprintf(
                '<picture><source srcset="%s" type="image/webp"><img src="%s" alt="%s"%s></picture>',
                asset($webpPath),
                asset($path),
                htmlspecialchars($alt, ENT_QUOTES),
                $attrString
            );
        }

        return sprintf(
            '<img src="%s" alt="%s"%s>',
            asset($path),
            htmlspecialchars($alt, ENT_QUOTES),
            $attrString
        );
    }

    /**
     * Generate background-image CSS with WebP support
     * 
     * @param string $path Image path relative to public
     * @return string CSS string with WebP support
     */
    public static function backgroundImage(string $path): string
    {
        $pathInfo = pathinfo($path);
        $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
        $publicPath = public_path($webpPath);
        $hasWebp = file_exists($publicPath);

        if ($hasWebp) {
            return sprintf(
                'background-image:url("%s");background-image:image-set(url("%s") type("image/webp"),url("%s"))',
                asset($webpPath),
                asset($webpPath),
                asset($path)
            );
        }

        return sprintf('background-image:url("%s")', asset($path));
    }
}
