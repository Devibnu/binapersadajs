<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'module',
        'description',
        'subject_type',
        'subject_id',
        'ip_address',
        'user_agent',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actionBadgeClass(): string
    {
        return match ($this->action) {
            'create', 'approve' => 'bg-gradient-success',
            'update', 'email', 'reply', 'mark_read' => 'bg-gradient-info',
            'mark_replied' => 'bg-gradient-success',
            'delete', 'reject' => 'bg-gradient-danger',
            'upload' => 'bg-gradient-warning',
            'login', 'logout' => 'bg-gradient-secondary',
            default => 'bg-gradient-dark',
        };
    }
}
