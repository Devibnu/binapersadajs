<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class EmailTemplate extends Model
{
    protected $fillable = [
        'company_name',
        'sender_email',
        'sender_name',
        'website',
        'phone',
        'whatsapp',
        'address',
        'logo',
        'header_background',
        'footer_background',
        'header_color',
        'footer_color',
        'button_color',
        'text_color',
        'header_html',
        'footer_html',
        'disclaimer_html',
    ];

    public static function defaults(): array
    {
        $websiteSetting = WebsiteSetting::query()->first();
        $emailSetting = EmailSetting::active();

        return [
            'company_name' => $websiteSetting?->nama_perusahaan ?: 'PT. Bina Persada Jaya Sejahtera',
            'sender_email' => $emailSetting?->from_address ?: $websiteSetting?->email ?: config('mail.from.address'),
            'sender_name' => $emailSetting?->from_name ?: $websiteSetting?->nama_perusahaan ?: 'PT. Bina Persada Jaya Sejahtera',
            'website' => config('app.url'),
            'phone' => $websiteSetting?->telepon,
            'whatsapp' => $websiteSetting?->whatsapp,
            'address' => $websiteSetting?->alamat,
            'logo' => $websiteSetting?->logo,
            'header_background' => null,
            'footer_background' => null,
            'header_color' => '#0c1e35',
            'footer_color' => '#0c1e35',
            'button_color' => '#1f8f5f',
            'text_color' => '#263544',
            'header_html' => '<div style="text-align:left;">{{logo}}<div style="font-size:20px;font-weight:bold;margin-top:14px;">{{company_name}}</div></div>',
            'footer_html' => '<strong>{{company_name}}</strong><br>{{address}}<br>Telp: {{phone}} | WhatsApp: {{whatsapp}}<br>Email: {{email}} | Website: {{website}}',
            'disclaimer_html' => 'Email ini dikirim otomatis oleh sistem PT. Bina Persada Jaya Sejahtera.',
        ];
    }

    public static function current(): self
    {
        if (! Schema::hasTable('email_templates')) {
            return new self(static::defaults());
        }

        $template = static::query()->first();

        if (! $template) {
            return new self(static::defaults());
        }

        foreach (static::defaults() as $field => $default) {
            if (blank($template->{$field}) && filled($default)) {
                $template->setAttribute($field, $default);
            }
        }

        return $template;
    }

    public function imageUrl(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return str_replace('http://', 'https://', $path);
        }

        $normalizedPath = ltrim($path, '/');
        if (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, strlen('storage/'));
        }

        if (! Storage::disk('public')->exists($normalizedPath)) {
            return null;
        }

        return rtrim($this->emailAssetBaseUrl(), '/') . '/storage/' . ltrim($normalizedPath, '/');
    }

    public function emailAssetBaseUrl(): string
    {
        $baseUrl = $this->website ?: config('app.url') ?: 'https://binapersadajs.co.id';
        $host = parse_url($baseUrl, PHP_URL_HOST);

        if (! $host || str_ends_with($host, '.test') || in_array($host, ['localhost', '127.0.0.1'], true)) {
            return 'https://binapersadajs.co.id';
        }

        return preg_replace('/^http:\/\//i', 'https://', rtrim($baseUrl, '/'));
    }

    public function logoUrl(): ?string
    {
        return $this->imageUrl($this->logo);
    }

    public function headerBackgroundUrl(): ?string
    {
        return $this->imageUrl($this->header_background);
    }

    public function footerBackgroundUrl(): ?string
    {
        return $this->imageUrl($this->footer_background);
    }
}
