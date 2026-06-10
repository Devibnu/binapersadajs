<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortalConversation extends Model
{
    public const MODULE_INQUIRY = 'inquiry';
    public const MODULE_PROJECT_REPORT = 'project_report';
    public const MODULE_INVOICE_REPORT = 'invoice_report';

    protected $fillable = [
        'module_type',
        'module_id',
        'sender_type',
        'sender_id',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function scopeForModule(Builder $query, string $moduleType, int $moduleId): Builder
    {
        return $query->where('module_type', $moduleType)
            ->where('module_id', $moduleId);
    }

    public function senderAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function senderClient(): BelongsTo
    {
        return $this->belongsTo(IqmUser::class, 'sender_id');
    }

    public function senderName(): string
    {
        if ($this->sender_type === 'admin') {
            return $this->senderAdmin?->name ?: 'Admin';
        }

        $client = $this->senderClient;

        return $client
            ? trim($client->company_name . ' - ' . $client->pic_name)
            : 'Client';
    }

    public function moduleLabel(): string
    {
        return match ($this->module_type) {
            self::MODULE_PROJECT_REPORT => 'Project Report',
            self::MODULE_INVOICE_REPORT => 'Invoice Report',
            default => 'Inquiry',
        };
    }
}
