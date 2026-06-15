<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectReportAttachment extends Model
{
    protected $fillable = [
        'project_report_id',
        'file_name',
        'original_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function projectReport(): BelongsTo
    {
        return $this->belongsTo(ProjectReport::class);
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function formattedSize(): string
    {
        $size = (int) $this->file_size;

        if ($size <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = (int) floor(log($size, 1024));
        $power = max(0, min($power, count($units) - 1));

        return round($size / (1024 ** $power), 2) . ' ' . $units[$power];
    }
}
