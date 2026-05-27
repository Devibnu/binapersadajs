<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class AboutPageSetting extends Model
{
    protected $fillable = [
        'hero_title',
        'hero_breadcrumb',
        'hero_image',
        'section_label',
        'section_title',
        'section_description',
        'quote_text',
        'section_description_bottom',
        'slider_1_title',
        'slider_1_image',
        'slider_2_title',
        'slider_2_image',
        'slider_3_title',
        'slider_3_image',
        'counter_1_number',
        'counter_1_label',
        'counter_1_icon',
        'counter_2_number',
        'counter_2_label',
        'counter_2_icon',
        'counter_3_number',
        'counter_3_label',
        'counter_3_icon',
        'counter_4_number',
        'counter_4_label',
        'counter_4_icon',
        'team_label',
        'team_title',
    ];

    public static function defaults(): array
    {
        return [
            'hero_title' => 'About',
            'hero_breadcrumb' => 'About Us',
            'hero_image' => 'web/images/banner/banner1.jpg',
            'section_label' => 'Who We Are',
            'section_title' => null,
            'section_description' => 'PT. Bina Persada Jaya Sejahtera adalah perusahaan contractor dan fabrication yang mendukung pekerjaan industri, maintenance, construction, serta kebutuhan general supplier.',
            'quote_text' => 'Kami mengutamakan keselamatan kerja, kualitas pelaksanaan, dan koordinasi lapangan yang bertanggung jawab pada setiap proyek.',
            'section_description_bottom' => 'Dengan dukungan tim yang siap bekerja di lapangan, kami berkomitmen memberikan solusi praktis bagi kebutuhan proyek dan operasional pelanggan.',
            'slider_1_title' => 'Leadership',
            'slider_1_image' => 'web/images/slider-pages/slide-page1.jpg',
            'slider_2_title' => 'Relationships',
            'slider_2_image' => 'web/images/slider-pages/slide-page2.jpg',
            'slider_3_title' => 'Performance',
            'slider_3_image' => 'web/images/slider-pages/slide-page3.jpg',
            'counter_1_number' => '1789',
            'counter_1_label' => 'Total Projects',
            'counter_1_icon' => 'fa-hard-hat',
            'counter_2_number' => '647',
            'counter_2_label' => 'Staff Members',
            'counter_2_icon' => 'fa-users',
            'counter_3_number' => '4000',
            'counter_3_label' => 'Hours of Work',
            'counter_3_icon' => 'fa-clock',
            'counter_4_number' => '44',
            'counter_4_label' => 'Countries Experience',
            'counter_4_icon' => 'fa-globe',
            'team_label' => 'Quality Service',
            'team_title' => 'Professional Team',
        ];
    }

    public static function current(): self
    {
        if (! Schema::hasTable('about_page_settings')) {
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

    public function imageUrl(string $field): string
    {
        $image = $this->{$field};

        if (str_starts_with($image, 'web/') || str_starts_with($image, 'assets/')) {
            return asset($image);
        }

        if (str_starts_with($image, '/')) {
            return asset(ltrim($image, '/'));
        }

        return Storage::url($image);
    }
}
