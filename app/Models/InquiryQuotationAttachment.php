<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class InquiryQuotationAttachment extends Model
{
    use HasFactory;

    protected $table = 'inquiry_quotation_attachments';

    protected $fillable = [
        'inquiry_quotation_id', 'file_name', 'original_name', 'file_path', 'file_type', 'mime_type', 'file_size', 'attachment_type', 'uploaded_by'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function inquiryQuotation(): BelongsTo
    {
        return $this->belongsTo(InquiryQuotation::class, 'inquiry_quotation_id');
    }

    public function attachmentTypeLabel(): string
    {
        return match ($this->attachment_type) {
            'survey_photo' => 'Foto Survey',
            'survey_document' => 'Dokumen Survey',
            'quotation_pdf' => 'PDF Quotation',
            'client_document' => 'Dokumen Klien',
            'other' => 'Lainnya',
            default => ucfirst((string) $this->attachment_type),
        };
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function fileUrl(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function formattedSize(): string
    {
        $size = (int) $this->file_size;
        if ($size <= 0) return '0 B';
        $units = ['B','KB','MB','GB'];
        $power = (int) floor(log($size, 1024));
        $power = max(0, min($power, count($units) - 1));
        return round($size / (1024 ** $power), 2) . ' ' . $units[$power];
    }
}
