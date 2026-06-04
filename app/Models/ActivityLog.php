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
        return match ($this->normalizedAction()) {
            'create', 'approve' => 'bg-gradient-success',
            'update', 'email', 'test_email', 'send_email', 'reply_email', 'forward_email', 'download_file' => 'bg-gradient-info',
            'reply' => 'bg-gradient-info',
            'mark_replied', 'status_change', 'restore_email', 'login_account' => 'bg-gradient-success',
            'delete', 'delete_email' => 'bg-gradient-danger',
            'reject', 'save_draft' => 'bg-gradient-warning',
            'upload', 'upload_file' => 'bg-gradient-warning',
            'login', 'logout' => 'bg-gradient-secondary',
            default => 'bg-gradient-dark',
        };
    }

    public function actionLabel(): string
    {
        return ucwords(str_replace('_', ' ', $this->action));
    }

    public function normalizedAction(): string
    {
        return strtolower((string) $this->action);
    }

    public function displayUserName(): string
    {
        return $this->user_name ?: $this->user?->name ?: 'System';
    }

    public function displayUserEmail(): string
    {
        return data_get($this->properties, 'user_email')
            ?: $this->user?->email
            ?: 'Email tidak tersedia';
    }

    public function displayRoleName(): string
    {
        return data_get($this->properties, 'role_name')
            ?: $this->user?->role?->name
            ?: 'System';
    }

    public function recordName(): ?string
    {
        return data_get($this->properties, 'record_name')
            ?: data_get($this->properties, 'name')
            ?: data_get($this->properties, 'title')
            ?: data_get($this->properties, 'email')
            ?: data_get($this->properties, 'to')
            ?: data_get($this->properties, 'subject');
    }

    public function recordId(): ?string
    {
        return data_get($this->properties, 'model_id')
            ?: data_get($this->properties, 'record_id')
            ?: $this->subject_id;
    }

    public function modelType(): ?string
    {
        return data_get($this->properties, 'model_type') ?: $this->subject_type;
    }

    public function browserLabel(): string
    {
        $agent = (string) $this->user_agent;

        return match (true) {
            str_contains($agent, 'Edg/') => 'Microsoft Edge',
            str_contains($agent, 'Chrome/') => 'Chrome',
            str_contains($agent, 'Firefox/') => 'Firefox',
            str_contains($agent, 'Safari/') => 'Safari',
            str_contains($agent, 'PostmanRuntime') => 'Postman',
            $agent !== '' => 'Browser / Client lain',
            default => '-',
        };
    }

    public function structuredDescription(): string
    {
        $recordName = $this->recordName();

        if (! $recordName) {
            return $this->description ?: '-';
        }

        $verb = match ($this->normalizedAction()) {
            'create' => 'Menambahkan',
            'update' => 'Mengubah',
            'delete' => 'Menghapus',
            'approve' => 'Menyetujui',
            'reject' => 'Menolak',
            'reply', 'mark_replied', 'reply_email' => 'Mengirim balasan',
            'email', 'test_email', 'send_email', 'forward_email' => 'Mengirim email',
            'upload', 'upload_file' => 'Mengupload file',
            'download_file' => 'Mengunduh file',
            'status_change', 'assign_role' => 'Mengubah status',
            'delete_email' => 'Menghapus email',
            'restore_email' => 'Memulihkan email',
            'save_draft' => 'Menyimpan draft',
            'login_account' => 'Mengelola account',
            'login' => 'Login',
            'logout' => 'Logout',
            default => $this->actionLabel(),
        };

        return trim($verb . ' ' . $this->module . ': ' . $recordName);
    }
}
