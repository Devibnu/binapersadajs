<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class InquiryQuotation extends Model
{
    use HasFactory;

    protected $table = 'inquiry_quotations';

    protected $fillable = [
        'inquiry_number', 'inquiry_date', 'inquiry_by',
        'client_name', 'client_pic', 'client_phone', 'client_email', 'client_logo', 'iqm_user_id', 'visibility', 'client_address',
        'subject', 'description', 'pic_internal',
        'site_survey_status', 'site_survey_date', 'site_survey_notes',
        'quotation_number', 'quotation_date', 'deadline', 'amount', 'quotation_status',
        'notes', 'created_by'
    ];

    protected $casts = [
        'inquiry_date' => 'date',
        'site_survey_date' => 'date',
        'quotation_date' => 'date',
        'deadline' => 'date',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function attachments(): HasMany
    {
        return $this->hasMany(InquiryQuotationAttachment::class, 'inquiry_quotation_id');
    }

    public function iqmUser(): BelongsTo
    {
        return $this->belongsTo(IqmUser::class, 'iqm_user_id');
    }

    public function iqmUsers(): BelongsToMany
    {
        return $this->belongsToMany(IqmUser::class, 'inquiry_quotation_iqm_user')
            ->withTimestamps();
    }

    public function isPublic(): bool
    {
        return $this->visibility === 'public';
    }

    public function clientLogoUrl(): ?string
    {
        if (! $this->client_logo) {
            return null;
        }

        return Storage::disk('public')->url($this->client_logo);
    }

    public function inquiryByLabel(): string
    {
        return match ($this->inquiry_by) {
            'email' => 'Email',
            'whatsapp' => 'WhatsApp',
            'phone' => 'Telepon',
            'site_instruction' => 'Instruksi Lokasi',
            'meeting' => 'Meeting',
            'referral' => 'Referral',
            'other' => 'Lainnya',
            default => ucfirst((string) $this->inquiry_by),
        };
    }

    public function surveyStatusLabel(): string
    {
        return match ($this->site_survey_status) {
            'not_required' => 'Tidak Diperlukan',
            'scheduled' => 'Dijadwalkan',
            'done' => 'Selesai',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst((string) $this->site_survey_status),
        };
    }

    public function surveyStatusBadgeClass(): string
    {
        return match ($this->site_survey_status) {
            'not_required' => 'bg-gradient-secondary',
            'scheduled' => 'bg-gradient-warning',
            'done' => 'bg-gradient-success',
            'completed' => 'bg-gradient-success',
            'cancelled' => 'bg-gradient-danger',
            default => 'bg-gradient-info',
        };
    }

    public function quotationStatusLabel(): string
    {
        return match ($this->quotation_status) {
            'draft' => 'Draft',
            'process' => 'Proses',
            'submitted' => 'Dikirim',
            'revision' => 'Revisi',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'closed' => 'Ditutup',
            default => ucfirst((string) $this->quotation_status),
        };
    }

    public function quotationStatusBadgeClass(): string
    {
        return match ($this->quotation_status) {
            'draft' => 'bg-gradient-secondary',
            'process' => 'bg-gradient-info',
            'submitted' => 'bg-gradient-primary',
            'revision' => 'bg-gradient-warning',
            'approved' => 'bg-gradient-success',
            'rejected' => 'bg-gradient-danger',
            'closed' => 'bg-gradient-dark',
            default => 'bg-gradient-light',
        };
    }

    public function formattedAmount(): string
    {
        if (! $this->amount) {
            return '-';
        }

        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function whatsappUrl(): ?string
    {
        if (! $this->client_phone) {
            return null;
        }

        $phone = preg_replace('/\D+/', '', $this->client_phone);

        if (! $phone) {
            return null;
        }

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $text = urlencode("Halo, saya ingin menanyakan tentang \"{$this->subject}\" (No: {$this->inquiry_number})");
        return 'https://wa.me/' . $phone . '?text=' . $text;
    }
}
