<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ResponsiveImage extends Component
{
    public function __construct(
        public string $src,
        public string $alt,
        public ?string $class = null,
        public ?int $width = null,
        public ?int $height = null,
        public bool $lazy = true,
        public ?string $sizes = null,
    ) {}

    public function render()
    {
        // Determine if image has WebP version
        $pathInfo = pathinfo($this->src);
        $webpSrc = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
        $publicPath = public_path($webpSrc);
        $hasWebp = file_exists($publicPath);

        // Build loading attribute
        $loading = $this->lazy ? 'lazy' : 'eager';

        // Build image attributes
        $attrs = [
            'src' => asset($this->src),
            'alt' => $this->alt,
            'class' => $this->class,
            'loading' => $loading,
            'decoding' => 'async',
        ];

        if ($this->width) $attrs['width'] = $this->width;
        if ($this->height) $attrs['height'] = $this->height;

        // Return early if no WebP version exists
        if (!$hasWebp) {
            return view('components.responsive-image', [
                'hasWebp' => false,
                'attrs' => $attrs,
            ]);
        }

        // Build srcset for responsive images
        $srcset = [];
        if ($this->sizes) {
            // Parse sizes string like "360|600|1200" for different widths
            foreach (explode('|', $this->sizes) as $size) {
                $resizedPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . "-{$size}w.webp";
                if (file_exists(public_path($resizedPath))) {
                    $srcset[] = asset($resizedPath) . ' ' . $size . 'w';
                }
            }
        }

        return view('components.responsive-image', [
            'hasWebp' => true,
            'webpSrc' => asset($webpSrc),
            'srcset' => $srcset,
            'attrs' => $attrs,
        ]);
    }
}
