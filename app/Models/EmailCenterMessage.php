<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailCenterMessage extends Model
{
    protected $fillable = [
        'email_account_id',
        'user_id',
        'folder',
        'from_email',
        'to_email',
        'cc',
        'bcc',
        'subject',
        'body',
        'use_template',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'use_template' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class, 'email_account_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(EmailCenterAttachment::class);
    }
}
