<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WebsiteSetting extends Model
{
    protected $fillable = [
        'nama_perusahaan',
        'deskripsi_perusahaan',
        'logo',
        'favicon',
        'email',
        'telepon',
        'whatsapp',
        'alamat',
        'google_maps',
        'certificate_label',
        'certificate_value',
        'footer_text',
        'facebook',
        'instagram',
        'linkedin',
        'youtube',
    ];

    public function logoUrl(): string
    {
        return $this->logo ? Storage::url($this->logo) : asset('web/images/logo.png');
    }

    public function faviconUrl(): string
    {
        return $this->favicon ? Storage::url($this->favicon) : asset('icons/favicon-32x32.png');
    }
}
