<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Service extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'content',
        'short_content',
        'feature_1',
        'feature_2',
        'feature_3',
        'feature_4',
        'gallery_image_1',
        'gallery_image_2',
        'gallery_image_3',
        'faq_1_question',
        'faq_1_answer',
        'faq_2_question',
        'faq_2_answer',
        'faq_3_question',
        'faq_3_answer',
        'cta_text',
        'cta_button_text',
        'cta_button_link',
        'icon',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function imageUrl(): string
    {
        return $this->image
            ? Storage::url($this->image)
            : asset('web/images/services/' . $this->fallbackImage());
    }

    public function iconUrl(): ?string
    {
        return $this->icon ? Storage::url($this->icon) : null;
    }

    public function galleryImages(): array
    {
        return array_values(array_filter(array_map(
            fn (string $field) => $this->mediaUrl($this->{$field}),
            ['gallery_image_1', 'gallery_image_2', 'gallery_image_3']
        )));
    }

    public function features(): array
    {
        return array_values(array_filter([
            $this->feature_1,
            $this->feature_2,
            $this->feature_3,
            $this->feature_4,
        ]));
    }

    public function faqs(): array
    {
        $faqs = [];

        for ($number = 1; $number <= 3; $number++) {
            $question = $this->{"faq_{$number}_question"};
            $answer = $this->{"faq_{$number}_answer"};

            if ($question && $answer) {
                $faqs[] = compact('question', 'answer');
            }
        }

        return $faqs;
    }

    public function mediaUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'web/') || str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        if (str_starts_with($path, '/')) {
            return asset(ltrim($path, '/'));
        }

        return Storage::url($path);
    }

    public function iconClass(): string
    {
        return match ($this->slug) {
            'electrical-work' => 'fa-bolt',
            'fabrication' => 'fa-drafting-compass',
            'maintenance' => 'fa-tools',
            'scaffolding' => 'fa-layer-group',
            'manpower-supply' => 'fa-users-cog',
            'piping' => 'fa-project-diagram',
            'civil-construction' => 'fa-road',
            default => 'fa-cogs',
        };
    }

    private function fallbackImage(): string
    {
        return match ($this->slug) {
            'electrical-work', 'civil-construction' => 'service2.jpg',
            'fabrication' => 'service3.jpg',
            'maintenance' => 'service4.jpg',
            'scaffolding' => 'service5.jpg',
            'manpower-supply' => 'service6.jpg',
            default => 'service1.jpg',
        };
    }
}
