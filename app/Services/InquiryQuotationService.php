<?php

namespace App\Services;

use App\Models\InquiryQuotation;
use App\Models\InquiryQuotationAttachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class InquiryQuotationService
{
    /**
     * Generate next inquiry number
     * Format: INQ-YYYY-0001
     */
    public function generateInquiryNumber(): string
    {
        $year = now()->year;
        $prefix = 'INQ-' . $year . '-';

        $lastInquiry = InquiryQuotation::where('inquiry_number', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        if (! $lastInquiry) {
            $number = 1;
        } else {
            $lastNumber = (int) substr($lastInquiry->inquiry_number, -4);
            $number = $lastNumber + 1;
        }

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate next quotation number
     * Format: QTN-YYYY-0001
     */
    public function generateQuotationNumber(): string
    {
        $year = now()->year;
        $prefix = 'QTN-' . $year . '-';

        $lastQuotation = InquiryQuotation::where('quotation_number', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        if (! $lastQuotation) {
            $number = 1;
        } else {
            $lastNumber = (int) substr($lastQuotation->quotation_number, -4);
            $number = $lastNumber + 1;
        }

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Upload attachment file
     */
    public function uploadAttachment(InquiryQuotation $inquiry, UploadedFile $file, string $attachmentType, ?string $uploadedBy = null): InquiryQuotationAttachment
    {
        // Determine file type
        $fileExtension = strtolower($file->getClientOriginalExtension());
        $fileType = $this->getFileType($fileExtension);
        $mimeType = $file->getClientMimeType();

        // Generate unique filename
        $year = now()->year;
        $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        $fileName = time() . '_' . uniqid() . '.' . $fileExtension;

        // Store file
        $path = $file->storeAs(
            "inquiry-quotations/{$year}/{$month}",
            $fileName,
            'public'
        );

        // Create attachment record
        return InquiryQuotationAttachment::create([
            'inquiry_quotation_id' => $inquiry->id,
            'file_name' => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $fileType,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
            'attachment_type' => $attachmentType,
            'uploaded_by' => $uploadedBy,
        ]);
    }

    /**
     * Delete attachment file and record
     */
    public function deleteAttachment(InquiryQuotationAttachment $attachment): bool
    {
        // Delete file from storage
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        // Delete record
        return $attachment->delete();
    }

    /**
     * Determine file type from extension
     */
    private function getFileType(string $extension): string
    {
        return match (strtolower($extension)) {
            'pdf' => 'pdf',
            'jpg', 'jpeg' => 'jpg',
            'png' => 'png',
            'webp' => 'webp',
            default => 'other',
        };
    }

    /**
     * Validate file can be uploaded
     */
    public function validateFile(UploadedFile $file): array
    {
        $errors = [];

        // Check file size (max 10MB = 10240 KB)
        if ($file->getSize() > 10240 * 1024) {
            $errors[] = 'Ukuran file tidak boleh lebih dari 10MB';
        }

        // Check file type
        $allowedMimes = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower($file->getClientOriginalExtension());
        if (! in_array($extension, $allowedMimes)) {
            $errors[] = 'Tipe file hanya boleh: PDF, JPG, JPEG, PNG, WebP';
        }

        return $errors;
    }

    /**
     * Get attachment download path
     */
    public function getAttachmentDownloadPath(InquiryQuotationAttachment $attachment): string
    {
        return Storage::disk('public')->path($attachment->file_path);
    }
}
