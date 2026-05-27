<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AboutTeam extends Model
{
    protected $fillable = [
        'name',
        'position',
        'description',
        'image',
        'linkedin_url',
        'instagram_url',
        'twitter_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function imageUrl(): string
    {
        if (! $this->image) {
            return asset('web/images/team/team1.jpg');
        }

        if (str_starts_with($this->image, 'web/') || str_starts_with($this->image, 'assets/')) {
            return asset($this->image);
        }

        if (str_starts_with($this->image, '/')) {
            return asset(ltrim($this->image, '/'));
        }

        return Storage::url($this->image);
    }
}
