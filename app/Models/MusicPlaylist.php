<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MusicPlaylist extends Model
{
    protected $fillable = [
        'title',
        'audio_file',
        'audio_url',
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
        return $query->orderBy('sort_order')->orderBy('title');
    }

    public function audioSource(): ?string
    {
        if ($this->audio_file) {
            return Storage::url($this->audio_file);
        }

        return $this->audio_url;
    }
}
