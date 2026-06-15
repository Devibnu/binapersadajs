<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceReportAttachment extends Model
{
    protected $fillable = [
        'invoice_report_id',
        'original_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function invoiceReport(): BelongsTo
    {
        return $this->belongsTo(InvoiceReport::class);
    }

    public function isImage(): bool
    {
        return in_array(strtolower((string) $this->file_type), ['jpg', 'jpeg', 'png'], true);
    }

    public function isPdf(): bool
    {
        return strtolower((string) $this->file_type) === 'pdf';
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
