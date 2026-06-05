<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WebsiteSetting extends Model
{
    protected $fillable = [
        'nama_perusahaan',
        'deskripsi_perusahaan',
        'logo',
        'favicon',
        'email',
        'telepon',
        'whatsapp',
        'alamat',
        'google_maps',
        'certificate_label',
        'certificate_value',
        'footer_text',
        'facebook',
        'instagram',
        'linkedin',
        'youtube',
    ];

    public function logoUrl(): string
    {
        if (! $this->hasPrimaryLogo()) {
            return asset('web/images/logo.png');
        }

        if (str_starts_with($this->logo, 'web/') || str_starts_with($this->logo, 'assets/')) {
            return asset($this->logo);
        }

        if (str_starts_with($this->logo, '/')) {
            return asset(ltrim($this->logo, '/'));
        }

        return Storage::url($this->logo);
    }

    public function assetVersion(): int
    {
        return $this->updated_at?->timestamp ?? time();
    }

    public function faviconUrl(): string
    {
        if ($this->favicon) {
            if (str_starts_with($this->favicon, 'web/') || str_starts_with($this->favicon, 'assets/') || str_starts_with($this->favicon, 'icons/')) {
                return asset($this->favicon);
            }

            if (str_starts_with($this->favicon, '/')) {
                return asset(ltrim($this->favicon, '/'));
            }

            return Storage::url($this->favicon);
        }

        return asset('icons/favicon-32x32.png');
    }

    public function appleTouchIconUrl(): string
    {
        return asset('icons/apple-touch-icon.png');
    }

    public function hasPrimaryLogo(): bool
    {
        return ! empty($this->logo) && ! $this->isIconLogoPath($this->logo);
    }

    private function isIconLogoPath(string $path): bool
    {
        $normalizedPath = strtolower(ltrim($path, '/'));
        $filename = basename($normalizedPath);

        if ($this->favicon && $normalizedPath === strtolower(ltrim($this->favicon, '/'))) {
            return true;
        }

        if (str_contains($normalizedPath, 'favicon')) {
            return true;
        }

        if (str_starts_with($normalizedPath, 'icons/') || str_starts_with($normalizedPath, 'pwa/icons/')) {
            return true;
        }

        return preg_match('/^(apple-touch-icon|android-chrome|maskable-icon|mstile|notification-icon|icon-\d+)/', $filename) === 1;
    }
}
