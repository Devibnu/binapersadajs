<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class HomepageVideo extends Model
{
    protected $fillable = [
        'section_label',
        'title',
        'description',
        'youtube_url',
        'thumbnail_image',
        'button_text',
        'button_link',
        'feature_1',
        'feature_2',
        'feature_3',
        'feature_4',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function defaults(): array
    {
        return [
            'section_label' => 'VIDEO COMPANY PROFILE',
            'title' => 'Aktivitas Project Industri Kami',
            'description' => 'PT. Bina Persada Jaya Sejahtera mendukung pekerjaan mechanical, fabrication, manpower supply, maintenance, dan shutdown project dengan tim profesional serta standar kerja berbasis HSE.',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'button_text' => 'Tonton Video',
            'button_link' => 'https://www.youtube.com/',
            'feature_1' => 'Mechanical Work',
            'feature_2' => 'Fabrication Support',
            'feature_3' => 'Shutdown Project',
            'feature_4' => 'Manpower Supply',
            'is_active' => true,
        ];
    }

    public static function current(): self
    {
        if (! Schema::hasTable('homepage_videos')) {
            return new self(static::defaults());
        }

        $setting = static::query()->first() ?: new self();

        foreach (static::defaults() as $field => $default) {
            if ($field === 'is_active') {
                if ($setting->{$field} === null) {
                    $setting->{$field} = $default;
                }

                continue;
            }

            if (blank($setting->{$field})) {
                $setting->{$field} = $default;
            }
        }

        return $setting;
    }

    public static function videoIdFromUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        $components = parse_url(trim($url));
        $host = strtolower(preg_replace('/^www\./', '', $components['host'] ?? ''));
        $path = trim($components['path'] ?? '', '/');
        $videoId = null;

        if ($host === 'youtu.be') {
            $videoId = explode('/', $path)[0] ?? null;
        } elseif (in_array($host, ['youtube.com', 'm.youtube.com', 'music.youtube.com'], true)) {
            if ($path === 'watch') {
                parse_str($components['query'] ?? '', $query);
                $videoId = $query['v'] ?? null;
            } elseif (preg_match('#^(embed|shorts)/([^/]+)#', $path, $matches)) {
                $videoId = $matches[2];
            }
        }

        return is_string($videoId) && preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId)
            ? $videoId
            : null;
    }

    public function embedUrl(): ?string
    {
        $videoId = static::videoIdFromUrl($this->youtube_url);

        return $videoId
            ? 'https://www.youtube.com/embed/' . $videoId . '?autoplay=1&rel=0'
            : null;
    }

    public function thumbnailUrl(): string
    {
        if (! $this->thumbnail_image) {
            return asset('web/images/projects/parallax1.jpg');
        }

        if (str_starts_with($this->thumbnail_image, 'web/') || str_starts_with($this->thumbnail_image, 'assets/')) {
            return asset($this->thumbnail_image);
        }

        return asset(Storage::url($this->thumbnail_image));
    }
}
