<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HeroBanner extends Model
{
    protected $table = 'hero_banners';

    protected $fillable = [
        'small_text',
        'title',
        'description',
        'button_text',
        'button_link',
        'image',
        'is_active',
        'sort_order',
        'content_position',
        'judul',
        'sub_judul',
        'teks_tombol',
        'link_tombol',
        'gambar_background',
        'status_aktif',
        'urutan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'status_aktif' => 'boolean',
        'urutan' => 'integer',
    ];

    public function backgroundUrl(): string
    {
        $image = $this->image ?: $this->gambar_background;

        if (! $image) {
            return asset('web/images/slider-main/bg1.jpg');
        }

        if (str_starts_with($image, 'web/') || str_starts_with($image, 'assets/')) {
            return asset($image);
        }

        if (str_starts_with($image, '/')) {
            return asset(ltrim($image, '/'));
        }

        return Storage::url($image);
    }

    public function getDisplaySmallTextAttribute(): ?string
    {
        return $this->small_text ?: $this->sub_judul;
    }

    public function getDisplayTitleAttribute(): ?string
    {
        return $this->title ?: $this->judul;
    }

    public function getDisplayDescriptionAttribute(): ?string
    {
        return $this->description;
    }

    public function getDisplayButtonTextAttribute(): ?string
    {
        return $this->button_text ?: $this->teks_tombol;
    }

    public function getDisplayButtonLinkAttribute(): ?string
    {
        return $this->button_link ?: $this->link_tombol;
    }

    public function getDisplayIsActiveAttribute(): bool
    {
        return (bool) ($this->is_active ?? $this->status_aktif);
    }

    public function getDisplaySortOrderAttribute(): int
    {
        return (int) ($this->sort_order ?? $this->urutan ?? 0);
    }

    public function resolvedContentPosition(int $position): string
    {
        if (in_array($this->content_position, ['center', 'left', 'right'], true)) {
            return $this->content_position;
        }

        if ($position === 1) {
            return 'center';
        }

        return $position % 2 === 0 ? 'left' : 'right';
    }
}
