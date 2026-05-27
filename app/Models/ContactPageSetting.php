<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ContactPageSetting extends Model
{
    protected $fillable = [
        'section_label',
        'heading',
        'address_title',
        'email_title',
        'phone_title',
        'map_embed',
        'form_heading',
        'success_message',
        'submit_button_text',
    ];

    public static function defaults(): array
    {
        return [
            'section_label' => 'Hubungi Kami',
            'heading' => 'Temukan Lokasi Kami',
            'address_title' => 'Kunjungi Kantor Kami',
            'email_title' => 'Email Kami',
            'phone_title' => 'Telepon Kami',
            'map_embed' => null,
            'form_heading' => 'Kirim Pesan',
            'success_message' => 'Pesan Anda berhasil dikirim. Tim kami akan segera menghubungi Anda.',
            'submit_button_text' => 'KIRIM PESAN',
        ];
    }

    public static function current(): self
    {
        if (! Schema::hasTable('contact_page_settings')) {
            return new self(static::defaults());
        }

        $setting = static::query()->first();

        if (! $setting) {
            return new self(static::defaults());
        }

        foreach (static::defaults() as $field => $default) {
            if (blank($setting->{$field})) {
                $setting->setAttribute($field, $default);
            }
        }

        return $setting;
    }
}
