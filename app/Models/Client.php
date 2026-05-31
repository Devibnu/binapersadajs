<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Client extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'website_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function logoUrl(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        if (str_starts_with($this->logo, 'web/') || str_starts_with($this->logo, 'assets/')) {
            return asset($this->logo);
        }

        if (str_starts_with($this->logo, '/')) {
            return asset(ltrim($this->logo, '/'));
        }

        return Storage::url($this->logo);
    }
}
