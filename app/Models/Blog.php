<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'category',
        'tags',
        'author_name',
        'published_at',
        'is_published',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where(function (Builder $query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    public function featuredImageUrl(string $fallback = 'web/images/news/news1.jpg'): string
    {
        return $this->imageUrl($this->featured_image, $fallback);
    }

    public function ogImageUrl(): string
    {
        return $this->imageUrl($this->og_image ?: $this->featured_image, 'web/images/news/news1.jpg');
    }

    public function displayAuthor(): string
    {
        return $this->author_name ?: 'Admin';
    }

    public function displayDate(): string
    {
        return ($this->published_at ?: $this->created_at)
            ?->locale('id')
            ->translatedFormat('d F Y') ?? '';
    }

    public function tagList(): array
    {
        return collect(preg_split('/[,;]+/', $this->tags ?? ''))
            ->map(fn (string $tag) => trim($tag))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function imageUrl(?string $path, string $fallback): string
    {
        if (! $path) {
            return asset($fallback);
        }

        if (str_starts_with($path, 'web/') || str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        if (str_starts_with($path, '/')) {
            return asset(ltrim($path, '/'));
        }

        return Storage::url($path);
    }
}
