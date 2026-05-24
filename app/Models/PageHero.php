<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PageHero extends Model
{
    protected $fillable = [
        'page_key',
        'title',
        'breadcrumb_text',
        'background_image',
        'overlay_opacity',
        'text_position',
        'is_active',
    ];

    protected $casts = [
        'overlay_opacity' => 'float',
        'is_active' => 'boolean',
    ];

    public function backgroundUrl(string $fallback = 'web/images/banner/banner1.jpg'): string
    {
        if (! $this->background_image) {
            return asset($fallback);
        }

        if (str_starts_with($this->background_image, 'web/') || str_starts_with($this->background_image, 'assets/')) {
            return asset($this->background_image);
        }

        if (str_starts_with($this->background_image, '/')) {
            return asset(ltrim($this->background_image, '/'));
        }

        return Storage::url($this->background_image);
    }

    public function textClass(): string
    {
        return match ($this->text_position) {
            'left' => 'text-left text-start',
            'right' => 'text-right text-end',
            default => 'text-center',
        };
    }

    public function breadcrumbClass(): string
    {
        return match ($this->text_position) {
            'left' => 'justify-content-start',
            'right' => 'justify-content-end',
            default => 'justify-content-center',
        };
    }
}
