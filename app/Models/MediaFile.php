<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaFile extends Model
{
    protected $fillable = [
        'original_name',
        'file_name',
        'disk',
        'path',
        'url',
        'mime_type',
        'extension',
        'size',
        'width',
        'height',
        'alt_text',
        'title',
        'uploaded_by',
    ];

    protected $casts = [
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function formattedSize(): string
    {
        $size = (float) $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;

        while ($size >= 1024 && $index < count($units) - 1) {
            $size /= 1024;
            $index++;
        }

        return number_format($size, $index === 0 ? 0 : 2, ',', '.') . ' ' . $units[$index];
    }

    public function dimensionsLabel(): string
    {
        if (! $this->width || ! $this->height) {
            return '-';
        }

        return $this->width . ' x ' . $this->height . ' px';
    }

    public function dimensions(): string
    {
        return $this->dimensionsLabel();
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function publicUrl(): string
    {
        $url = $this->url ?: Storage::disk($this->disk ?: 'public')->url($this->path);

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return asset($url);
    }
}
