<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'interest',
        'source',
        'message',
        'status',
        'ip_address',
        'user_agent',
    ];

    public function statusLabel(): string
    {
        return match ($this->status) {
            'contacted' => 'Dihubungi',
            'qualified' => 'Prospek',
            'converted' => 'Konversi',
            'closed' => 'Ditutup',
            default => 'Baru',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'contacted' => 'bg-gradient-info',
            'qualified' => 'bg-gradient-primary',
            'converted' => 'bg-gradient-success',
            'closed' => 'bg-gradient-secondary',
            default => 'bg-gradient-warning',
        };
    }

    public function sourceLabel(): string
    {
        return match ($this->source) {
            'footer' => 'Newsletter Footer',
            'blog-sidebar' => 'Subscribe Blog',
            'cta' => 'Minta Penawaran',
            'service-inquiry' => 'Service Inquiry',
            default => $this->source ? ucfirst($this->source) : '-',
        };
    }

    public function emailHistories(): HasMany
    {
        return $this->hasMany(LeadEmailHistory::class)->latest('sent_at');
    }

    public function whatsappUrl(): ?string
    {
        if (! filled($this->phone)) {
            return null;
        }

        $number = preg_replace('/\D+/', '', $this->phone);
        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        } elseif (str_starts_with($number, '8')) {
            $number = '62' . $number;
        }

        return 'https://wa.me/' . $number;
    }
}
