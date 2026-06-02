<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TradingProduct extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'image',
        'short_description',
        'description',
        'specifications',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function imageUrl(string $fallback = 'web/images/projects/project1.jpg'): string
    {
        if (! $this->image) {
            return asset($fallback);
        }

        if (str_starts_with($this->image, 'web/') || str_starts_with($this->image, 'assets/')) {
            return asset($this->image);
        }

        if (str_starts_with($this->image, '/')) {
            return asset(ltrim($this->image, '/'));
        }

        return Storage::url($this->image);
    }

    public function categoryKey(): string
    {
        return Str::slug($this->category ?: 'uncategorized');
    }
}
