<?php

namespace Tests\Feature;

use App\Helpers\ImageUploadHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ImageUploadHelperTest extends TestCase
{
    public function test_large_uploaded_image_is_accepted_resized_and_converted_to_webp(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()
            ->image('large-banner.jpg', 2400, 1200)
            ->size(8192);

        $validator = Validator::make(
            ['image' => $file],
            ['image' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240']]
        );

        $this->assertFalse($validator->fails());

        $path = ImageUploadHelper::uploadAndCompress($file, 'hero-banners', 1600);

        Storage::disk('public')->assertExists($path);
        $this->assertStringEndsWith('.webp', $path);

        [$width, $height] = getimagesize(Storage::disk('public')->path($path));

        $this->assertSame(1600, $width);
        $this->assertSame(800, $height);
    }

    public function test_old_uploaded_image_can_be_removed_after_replacement(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('services/old.webp', 'old-image');

        ImageUploadHelper::deleteStoredImage('services/old.webp');

        Storage::disk('public')->assertMissing('services/old.webp');
    }
}
