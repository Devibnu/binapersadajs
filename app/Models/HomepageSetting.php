<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class HomepageSetting extends Model
{
    protected $fillable = [
        'about_label',
        'about_title',
        'about_description',
        'about_feature_1_title',
        'about_feature_1_icon',
        'about_feature_2_title',
        'about_feature_2_icon',
        'about_feature_3_title',
        'about_feature_3_icon',
        'about_feature_4_title',
        'about_feature_4_icon',
        'values_title',
        'values_description',
        'value_1_title',
        'value_1_description',
        'value_2_title',
        'value_2_description',
        'value_3_title',
        'value_3_description',
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
        'service_section_label',
        'service_section_title',
        'quality_title',
        'quality_description',
        'quality_sub_description',
        'quality_item_1',
        'quality_item_2',
        'quality_item_3',
        'quality_item_4',
        'cta_phone_label',
        'cta_phone',
        'cta_title',
        'cta_description',
        'cta_button_text',
        'cta_button_link',
        'project_section_label',
        'project_section_title',
        'blog_label',
        'blog_title',
    ];

    public static function defaults(): array
    {
        return [
            'about_label' => 'About Company',
            'about_title' => 'Industrial Work Delivered with Safety, Speed, and Precision',
            'about_description' => 'PT. Bina Persada Jaya Sejahtera adalah perusahaan yang bergerak di bidang contractor, fabrication, maintenance, construction, dan general supplier untuk kebutuhan industri dan konstruksi.',
            'about_feature_1_title' => 'Safety First Execution',
            'about_feature_1_icon' => 'fa-hard-hat',
            'about_feature_2_title' => 'Field Ready Team',
            'about_feature_2_icon' => 'fa-tools',
            'about_feature_3_title' => 'Industrial Experience',
            'about_feature_3_icon' => 'fa-industry',
            'about_feature_4_title' => 'Long Term Partnership',
            'about_feature_4_icon' => 'fa-handshake',
            'values_title' => 'Our Values',
            'values_description' => 'Every project is handled with practical coordination, clear communication, and a strong commitment to HSE standards.',
            'value_1_title' => 'Health, Safety & Environment',
            'value_1_description' => 'Work planning, toolbox briefings, PPE discipline, and site coordination are treated as core project requirements.',
            'value_2_title' => 'Quality Workmanship',
            'value_2_description' => 'Fabrication, installation, and maintenance work are executed with attention to measurements, documentation, and handover readiness.',
            'value_3_title' => 'Responsive Support',
            'value_3_description' => 'We support project teams with flexible manpower, site material needs, and practical coordination for industrial activities.',
            'counter_1_number' => '8',
            'counter_1_label' => 'Core Services',
            'counter_1_icon' => 'fa-industry',
            'counter_2_number' => '100%',
            'counter_2_label' => 'HSE Commitment',
            'counter_2_icon' => 'fa-user-shield',
            'counter_3_number' => '24/7',
            'counter_3_label' => 'Project Support',
            'counter_3_icon' => 'fa-cogs',
            'counter_4_number' => '9001',
            'counter_4_label' => 'ISO Standard',
            'counter_4_icon' => 'fa-certificate',
            'service_section_label' => 'KAPABILITAS INDUSTRI',
            'service_section_title' => 'LAYANAN KAMI',
            'quality_title' => 'Quality & HSE Commitment',
            'quality_description' => 'Project work is only useful when it is delivered safely, documented clearly, and ready for operation.',
            'quality_sub_description' => 'Our field approach combines practical supervision, safety briefings, work permits, coordination with client teams, and clear project documentation.',
            'quality_item_1' => 'Safety work procedure',
            'quality_item_2' => 'Site coordination',
            'quality_item_3' => 'Project documentation',
            'quality_item_4' => 'Responsive support team',
            'cta_phone_label' => 'Need Site Support?',
            'cta_phone' => '0254-7871299',
            'cta_title' => 'Talk to Our Industrial Project Team',
            'cta_description' => 'Mechanical, fabrication, maintenance, piping, scaffolding, manpower supplier, and civil work.',
            'cta_button_text' => 'Contact Now',
            'cta_button_link' => '/contact',
            'project_section_label' => 'PROJECT ACTIVITY',
            'project_section_title' => 'INDUSTRIAL WORKS',
            'blog_label' => 'Company Updates',
            'blog_title' => 'Latest Blog',
        ];
    }

    public static function current(): self
    {
        if (! Schema::hasTable('homepage_settings')) {
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
