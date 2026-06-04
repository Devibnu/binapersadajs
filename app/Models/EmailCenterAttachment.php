<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EmailCenterAttachment extends Model
{
    protected $fillable = [
        'email_center_message_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(EmailCenterMessage::class, 'email_center_message_id');
    }

    public function absolutePath(): string
    {
        return Storage::disk('public')->path($this->file_path);
    }
}
