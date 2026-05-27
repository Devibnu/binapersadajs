<?php

namespace Tests\Feature;

use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_search_preview_download_and_delete_media_image(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('paneladmin.media-library.create'))
            ->assertOk()
            ->assertSee('Upload Media')
            ->assertSee('Gambar akan otomatis dioptimasi dan dikompres.');

        $this->actingAs($admin)
            ->post(route('paneladmin.media-library.store'), [
                'file' => UploadedFile::fake()->image('workshop-site.jpg', 2400, 1200)->size(4096),
                'title' => 'Workshop Site',
                'alt_text' => 'Kegiatan fabrication di workshop',
            ])
            ->assertRedirect(route('paneladmin.media-library.index'))
            ->assertSessionHas('success', 'Media berhasil diupload dan dioptimasi.');

        $mediaFile = MediaFile::query()->firstOrFail();

        $this->assertSame('workshop-site.jpg', $mediaFile->original_name);
        $this->assertSame('public', $mediaFile->disk);
        $this->assertSame('image/webp', $mediaFile->mime_type);
        $this->assertSame('webp', $mediaFile->extension);
        $this->assertStringStartsWith('media-library/' . now()->format('Y/m') . '/', $mediaFile->path);
        $this->assertStringEndsWith('.webp', $mediaFile->path);
        $this->assertNotNull($mediaFile->width);
        $this->assertNotNull($mediaFile->height);
        Storage::disk('public')->assertExists($mediaFile->path);

        $this->actingAs($admin)
            ->get(route('paneladmin.media-library.index', ['q' => 'Workshop', 'type' => 'images']))
            ->assertOk()
            ->assertSee('Workshop Site')
            ->assertSee('Copy URL')
            ->assertSee('navigator.clipboard.writeText', false);

        $this->actingAs($admin)
            ->get(route('paneladmin.media-library.index', ['q' => 'tidak-ada']))
            ->assertOk()
            ->assertDontSee('Workshop Site');

        $this->actingAs($admin)
            ->get(route('paneladmin.media-library.show', $mediaFile))
            ->assertOk()
            ->assertSee('Detail Media')
            ->assertSee('workshop-site.jpg')
            ->assertSee('Kegiatan fabrication di workshop')
            ->assertSee('Download');

        $this->actingAs($admin)
            ->get(route('paneladmin.media-library.download', $mediaFile))
            ->assertOk()
            ->assertDownload('workshop-site.jpg');

        $this->actingAs($admin)
            ->delete(route('paneladmin.media-library.destroy', $mediaFile))
            ->assertRedirect(route('paneladmin.media-library.index'))
            ->assertSessionHas('success', 'Media berhasil dihapus.');

        $this->assertDatabaseMissing('media_files', ['id' => $mediaFile->id]);
        Storage::disk('public')->assertMissing($mediaFile->path);
    }

    public function test_admin_can_upload_pdf_and_filter_documents_separately_from_images(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('paneladmin.media-library.store'), [
                'file' => UploadedFile::fake()->create('company-profile.pdf', 120, 'application/pdf'),
                'title' => 'Company Profile PDF',
            ])
            ->assertRedirect(route('paneladmin.media-library.index'))
            ->assertSessionHas('success', 'Media berhasil diupload.');

        $document = MediaFile::query()->firstOrFail();

        $this->assertSame('application/pdf', $document->mime_type);
        $this->assertSame('pdf', $document->extension);
        $this->assertNull($document->width);
        $this->assertNull($document->height);
        Storage::disk('public')->assertExists($document->path);

        $this->actingAs($admin)
            ->get(route('paneladmin.media-library.index', ['type' => 'documents']))
            ->assertOk()
            ->assertSee('Company Profile PDF')
            ->assertSee('Download');

        $this->actingAs($admin)
            ->get(route('paneladmin.media-library.index', ['type' => 'images']))
            ->assertOk()
            ->assertDontSee('Company Profile PDF');
    }

    public function test_media_upload_rejects_unsupported_file_and_file_over_five_megabytes(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('paneladmin.media-library.store'), [
                'file' => UploadedFile::fake()->create('notes.txt', 120, 'text/plain'),
            ])
            ->assertSessionHasErrors('file');

        $this->actingAs($admin)
            ->post(route('paneladmin.media-library.store'), [
                'file' => UploadedFile::fake()->image('oversized.jpg', 1200, 800)->size(6000),
            ])
            ->assertSessionHasErrors('file');

        $this->assertDatabaseCount('media_files', 0);
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Media',
            'email' => 'media-admin-' . uniqid() . '@example.com',
            'password' => bcrypt('secret'),
        ]);
    }
}
