<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'ip_address',
        'url',
        'path',
        'page_title',
        'referer',
        'user_agent',
        'browser',
        'platform',
        'device_type',
        'country',
        'city',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    public function maskedIp(): string
    {
        if (! filled($this->ip_address)) {
            return '-';
        }

        if (str_contains($this->ip_address, ':')) {
            $segments = explode(':', $this->ip_address);

            return implode(':', array_slice($segments, 0, 2)) . ':xxxx:xxxx';
        }

        $segments = explode('.', $this->ip_address);

        return count($segments) === 4
            ? $segments[0] . '.' . $segments[1] . '.xxx.xxx'
            : 'xxx.xxx.xxx.xxx';
    }

    public function refererLabel(): string
    {
        $referer = strtolower((string) $this->referer);

        return match (true) {
            $referer === '' => 'Direct',
            str_contains($referer, 'google.') => 'Google',
            str_contains($referer, 'facebook.') || str_contains($referer, 'fb.com') => 'Facebook',
            str_contains($referer, 'whatsapp.') || str_contains($referer, 'wa.me') => 'WhatsApp',
            str_contains($referer, 'instagram.') => 'Instagram',
            default => parse_url($this->referer, PHP_URL_HOST) ?: 'Lainnya',
        };
    }
}
