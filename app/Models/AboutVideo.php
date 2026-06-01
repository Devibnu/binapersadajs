<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AboutVideo extends Model
{
    protected $fillable = [
        'title',
        'youtube_url',
        'youtube_id',
        'thumbnail',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function embedUrl(): ?string
    {
        return $this->youtube_id ? 'https://www.youtube.com/embed/' . $this->youtube_id : null;
    }

    public function thumbnailUrl(): ?string
    {
        if ($this->thumbnail) {
            if (str_starts_with($this->thumbnail, 'web/') || str_starts_with($this->thumbnail, 'assets/')) {
                return asset($this->thumbnail);
            }

            if (str_starts_with($this->thumbnail, '/')) {
                return asset(ltrim($this->thumbnail, '/'));
            }

            return Storage::url($this->thumbnail);
        }

        return $this->youtube_id ? 'https://img.youtube.com/vi/' . $this->youtube_id . '/hqdefault.jpg' : null;
    }

    public function displayTitle(): string
    {
        return $this->title ?: 'Video About';
    }
}
