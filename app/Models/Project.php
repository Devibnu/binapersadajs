<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'client_name',
        'project_location',
        'project_year',
        'category',
        'project_category_id',
        'featured_image',
        'gallery_image_1',
        'gallery_image_2',
        'gallery_image_3',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function projectCategory(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class);
    }

    public function categoryName(): string
    {
        return $this->projectCategory?->name ?: ($this->category ?: 'Project');
    }

    public function featuredImageUrl(string $fallback = 'web/images/projects/project1.jpg'): string
    {
        return $this->mediaUrl($this->featured_image) ?: asset($fallback);
    }

    public function galleryImages(): array
    {
        return array_values(array_filter(array_map(
            fn (string $field) => $this->mediaUrl($this->{$field}),
            ['gallery_image_1', 'gallery_image_2', 'gallery_image_3']
        )));
    }

    public function categoryKey(): string
    {
        return $this->projectCategory?->slug ?: Str::slug($this->category ?: 'uncategorized');
    }

    public function mediaUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
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
