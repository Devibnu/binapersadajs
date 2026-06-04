<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailAccount extends Model
{
    protected $fillable = [
        'name',
        'email',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'imap_host',
        'imap_port',
        'imap_username',
        'imap_password',
        'imap_encryption',
        'is_active',
    ];

    protected $casts = [
        'smtp_password' => 'encrypted',
        'imap_password' => 'encrypted',
        'smtp_port' => 'integer',
        'imap_port' => 'integer',
        'is_active' => 'boolean',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(EmailCenterMessage::class);
    }

    public function applySmtpConfiguration(): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => $this->smtp_host,
            'mail.mailers.smtp.port' => $this->smtp_port,
            'mail.mailers.smtp.encryption' => $this->smtp_encryption ?: null,
            'mail.mailers.smtp.username' => $this->smtp_username,
            'mail.mailers.smtp.password' => $this->smtp_password,
            'mail.from.address' => $this->email,
            'mail.from.name' => $this->name,
        ]);
    }

    public function imapMailbox(string $folder = 'INBOX'): string
    {
        $flags = '/imap';
        if ($this->imap_encryption === 'ssl') {
            $flags .= '/ssl';
        } elseif ($this->imap_encryption === 'tls') {
            $flags .= '/tls';
        } else {
            $flags .= '/notls';
        }

        return sprintf('{%s:%d%s}%s', $this->imap_host, $this->imap_port ?: 993, $flags, $folder);
    }
}
