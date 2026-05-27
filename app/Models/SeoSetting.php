<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SeoSetting extends Model
{
    protected $fillable = [
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'canonical_url',
        'robots_index',
        'robots_follow',
        'google_site_verification',
        'google_analytics_id',
        'google_tag_manager',
        'schema_company_name',
        'schema_logo',
        'schema_phone',
        'schema_email',
        'schema_address',
        'schema_city',
        'schema_country',
        'schema_postal_code',
        'twitter_card_type',
        'twitter_site',
    ];

    protected $casts = [
        'robots_index' => 'boolean',
        'robots_follow' => 'boolean',
    ];

    public static function defaults(): array
    {
        return [
            'meta_title' => 'PT. Bina Persada Jaya Sejahtera - Industrial Contractor & Fabrication',
            'meta_description' => 'PT. Bina Persada Jaya Sejahtera menyediakan layanan contractor, fabrication, maintenance, construction, dan dukungan proyek industri.',
            'meta_keywords' => 'industrial contractor, fabrication, maintenance, construction, Bina Persada Jaya Sejahtera',
            'robots_index' => true,
            'robots_follow' => true,
            'schema_company_name' => 'PT. Bina Persada Jaya Sejahtera',
            'schema_country' => 'ID',
            'twitter_card_type' => 'summary_large_image',
        ];
    }

    public static function current(): self
    {
        if (! Schema::hasTable('seo_settings')) {
            return new self(static::defaults());
        }

        $setting = static::query()->first() ?: new self();

        foreach (static::defaults() as $field => $default) {
            $isBooleanSetting = in_array($field, ['robots_index', 'robots_follow'], true);

            if (($isBooleanSetting && $setting->{$field} === null) || (! $isBooleanSetting && blank($setting->{$field}))) {
                $setting->{$field} = $default;
            }
        }

        return $setting;
    }

    public function ogImageUrl(?WebsiteSetting $websiteSetting = null): string
    {
        if ($this->og_image) {
            return asset(Storage::url($this->og_image));
        }

        return $websiteSetting?->logoUrl() ?? asset('web/images/logo.png');
    }

    public function schemaLogoUrl(?WebsiteSetting $websiteSetting = null): string
    {
        if ($this->schema_logo) {
            return asset(Storage::url($this->schema_logo));
        }

        return $websiteSetting?->logoUrl() ?? asset('web/images/logo.png');
    }

    public function canonicalUrl(string $currentUrl): string
    {
        if (! $this->canonical_url) {
            return $currentUrl;
        }

        $base = rtrim($this->canonical_url, '/');
        $path = parse_url($currentUrl, PHP_URL_PATH) ?: '/';

        return $path === '/' ? $base . '/' : $base . $path;
    }

    public function robotsContent(): string
    {
        return ($this->robots_index ? 'index' : 'noindex') . ', ' . ($this->robots_follow ? 'follow' : 'nofollow');
    }
}
