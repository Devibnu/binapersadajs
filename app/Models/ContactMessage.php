<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'ip_address',
        'user_agent',
    ];

    public function replies(): HasMany
    {
        return $this->hasMany(ContactMessageReply::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'read' => 'Dibaca',
            'replied' => 'Dibalas',
            default => 'Belum Dibaca',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'read' => 'bg-gradient-info',
            'replied' => 'bg-gradient-success',
            default => 'bg-gradient-warning',
        };
    }

    public function whatsappUrl(): ?string
    {
        if (! $this->phone) {
            return null;
        }

        $phone = preg_replace('/\D+/', '', $this->phone);

        if (! $phone) {
            return null;
        }

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return 'https://wa.me/' . $phone;
    }
}
