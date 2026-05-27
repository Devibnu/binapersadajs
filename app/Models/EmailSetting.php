<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class EmailSetting extends Model
{
    protected $fillable = [
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name',
        'is_active',
    ];

    protected $casts = [
        'password' => 'encrypted',
        'port' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function active(): ?self
    {
        if (! Schema::hasTable('email_settings')) {
            return null;
        }

        return static::query()->where('is_active', true)->first();
    }

    public static function applyActiveConfiguration(): ?self
    {
        $setting = static::active();

        if ($setting) {
            $setting->applyConfiguration();
        }

        return $setting;
    }

    public function applyConfiguration(): void
    {
        config([
            'mail.default' => $this->mailer,
            'mail.mailers.' . $this->mailer . '.transport' => 'smtp',
            'mail.mailers.' . $this->mailer . '.host' => $this->host,
            'mail.mailers.' . $this->mailer . '.port' => $this->port,
            'mail.mailers.' . $this->mailer . '.encryption' => $this->encryption ?: null,
            'mail.mailers.' . $this->mailer . '.username' => $this->username,
            'mail.mailers.' . $this->mailer . '.password' => $this->password,
            'mail.from.address' => $this->from_address,
            'mail.from.name' => $this->from_name,
        ]);
    }
}
