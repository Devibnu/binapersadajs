<?php

namespace Tests\Feature;

use App\Models\InquiryQuotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class InquiryQuotationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock Gate to allow permission checks during tests
        Gate::shouldReceive('forUser')->andReturnSelf();
        Gate::shouldReceive('allows')->andReturn(true);
        Gate::shouldReceive('check')->andReturn(true);
    }

    public function test_index_page_can_be_viewed(): void
    {
        $this->actingAs($this->admin())
            ->get(route('paneladmin.inquiry-quotations.index'))
                    ->assertOk();
    }

    public function test_create_page_can_be_viewed(): void
    {
        $this->actingAs($this->admin())
            ->get(route('paneladmin.inquiry-quotations.create'))
                    ->assertOk();
    }

    public function test_store_inquiry_successfully(): void
    {
        $admin = $this->admin();
        $payload = $this->validInquiryPayload();

        $response = $this->actingAs($admin)
            ->post(route('paneladmin.inquiry-quotations.store'), $payload);

        $response->assertStatus(302);

        $inquiry = InquiryQuotation::latest()->first();
        $this->assertNotNull($inquiry, 'Inquiry was not created. Response: ' . $response->getContent());

        $response->assertRedirect(route('paneladmin.inquiry-quotations.show', $inquiry->id));
        $this->assertDatabaseHas('inquiry_quotations', [
            'client_name' => 'PT Contoh Klien',
            'subject' => 'Permintaan Penawaran',
        ]);
    }

    public function test_show_inquiry_can_be_viewed(): void
    {
        $inquiry = InquiryQuotation::create($this->validInquiryPayload());

        $this->actingAs($this->admin())
            ->get(route('paneladmin.inquiry-quotations.show', $inquiry))
            ->assertOk()
            ->assertSee('PT Contoh Klien')
            ->assertSee('Permintaan Penawaran');
    }

    public function test_edit_page_can_be_viewed(): void
    {
        $inquiry = InquiryQuotation::create($this->validInquiryPayload());

        $this->actingAs($this->admin())
            ->get(route('paneladmin.inquiry-quotations.edit', $inquiry))
            ->assertOk()
            ->assertSee('Perbarui Inquiry');
    }

    public function test_update_inquiry_successfully(): void
    {
        $inquiry = InquiryQuotation::create($this->validInquiryPayload());

        $this->actingAs($this->admin())
            ->put(route('paneladmin.inquiry-quotations.update', $inquiry), [
                'inquiry_date' => now()->format('Y-m-d'),
                'inquiry_by' => 'email',
                'client_name' => 'PT Klien Diperbarui',
                'client_pic' => 'Ibu Dian',
                'client_phone' => '081234567890',
                'client_email' => 'dian@example.com',
                'visibility' => 'public',
                'iqm_user_id' => null,
                'client_address' => 'Jalan Baru No 2',
                'subject' => 'Permintaan Revisi Penawaran',
                'description' => 'Perbarui detail proyek.',
                'pic_internal' => 'Admin A',
                'site_survey_status' => 'scheduled',
                'site_survey_date' => now()->addDays(2)->format('Y-m-d'),
                'site_survey_notes' => 'Survey dijadwalkan minggu depan.',
                'quotation_number' => 'QTN-2026-0001',
                'quotation_date' => now()->addDays(3)->format('Y-m-d'),
                'deadline' => now()->addDays(10)->format('Y-m-d'),
                'amount' => 25000000,
                'quotation_status' => 'process',
                'notes' => 'Perubahan disetujui.',
            ])
            ->assertRedirect(route('paneladmin.inquiry-quotations.edit', $inquiry));

        $this->assertDatabaseHas('inquiry_quotations', [
            'id' => $inquiry->id,
            'client_name' => 'PT Klien Diperbarui',
            'quotation_status' => 'process',
        ]);
    }

    public function test_update_inquiry_normalizes_formatted_rupiah_amount(): void
    {
        $inquiry = InquiryQuotation::create($this->validInquiryPayload([
            'amount' => 15000000,
        ]));

        $this->actingAs($this->admin())
            ->put(route('paneladmin.inquiry-quotations.update', $inquiry), $this->validInquiryPayload([
                'inquiry_number' => $inquiry->inquiry_number,
                'quotation_number' => $inquiry->quotation_number,
                'amount' => '85.000.000',
            ]))
            ->assertRedirect(route('paneladmin.inquiry-quotations.edit', $inquiry));

        $inquiry->refresh();

        $this->assertSame('85000000.00', $inquiry->amount);
        $this->assertSame('Rp 85.000.000', $inquiry->formattedAmount());
    }

    public function test_can_update_inquiry_without_changing_quotation_number(): void
    {
        $inquiry = InquiryQuotation::create($this->validInquiryPayload([
            'quotation_number' => 'QTN-2026-0100',
            'client_name' => 'PT Client Awal',
        ]));

        $payload = $this->validInquiryPayload([
            'inquiry_number' => $inquiry->inquiry_number,
            'quotation_number' => $inquiry->quotation_number,
            'client_name' => 'PT Client Setelah Update',
            'subject' => 'Subject berhasil diperbarui',
        ]);

        $response = $this->actingAs($this->admin())
            ->from(route('paneladmin.inquiry-quotations.edit', $inquiry))
            ->put(route('paneladmin.inquiry-quotations.update', $inquiry), $payload);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('paneladmin.inquiry-quotations.edit', $inquiry));

        $this->assertDatabaseHas('inquiry_quotations', [
            'id' => $inquiry->id,
            'quotation_number' => 'QTN-2026-0100',
            'client_name' => 'PT Client Setelah Update',
            'subject' => 'Subject berhasil diperbarui',
        ]);
    }

    public function test_update_inquiry_rejects_quotation_number_used_by_another_record(): void
    {
        $existing = InquiryQuotation::create($this->validInquiryPayload([
            'quotation_number' => 'QTN-2026-0200',
        ]));
        $inquiry = InquiryQuotation::create($this->validInquiryPayload([
            'quotation_number' => 'QTN-2026-0201',
        ]));

        $payload = $this->validInquiryPayload([
            'inquiry_number' => $inquiry->inquiry_number,
            'quotation_number' => $existing->quotation_number,
            'client_name' => 'PT Client Gagal Update',
        ]);

        $this->actingAs($this->admin())
            ->from(route('paneladmin.inquiry-quotations.edit', $inquiry))
            ->put(route('paneladmin.inquiry-quotations.update', $inquiry), $payload)
            ->assertRedirect(route('paneladmin.inquiry-quotations.edit', $inquiry))
            ->assertSessionHasErrors(['quotation_number']);

        $this->assertDatabaseMissing('inquiry_quotations', [
            'id' => $inquiry->id,
            'quotation_number' => 'QTN-2026-0200',
        ]);
    }

    public function test_delete_inquiry_successfully(): void
    {
        $inquiry = InquiryQuotation::create($this->validInquiryPayload());

        $this->actingAs($this->admin())
            ->delete(route('paneladmin.inquiry-quotations.destroy', $inquiry))
            ->assertRedirect(route('paneladmin.inquiry-quotations.index'));

        $this->assertDatabaseMissing('inquiry_quotations', ['id' => $inquiry->id]);
    }

    public function test_upload_jpg_successfully(): void
    {
        Storage::fake('public');

        $payload = $this->validInquiryPayload();
        $payload['attachments'] = [UploadedFile::fake()->image('proposal.jpg')];

        $response = $this->actingAs($this->admin())
            ->post(route('paneladmin.inquiry-quotations.store'), $payload);

        $inquiry = InquiryQuotation::latest()->first();

        $response->assertRedirect(route('paneladmin.inquiry-quotations.show', $inquiry));
        $this->assertDatabaseHas('inquiry_quotation_attachments', [
            'inquiry_quotation_id' => $inquiry->id,
            'original_name' => 'proposal.jpg',
        ]);

        $attachment = $inquiry->attachments()->first();
        Storage::disk('public')->assertExists($attachment->file_path);
    }

    public function test_upload_pdf_successfully(): void
    {
        Storage::fake('public');

        $payload = $this->validInquiryPayload();
        $payload['attachments'] = [UploadedFile::fake()->create('dokumen.pdf', 1024, 'application/pdf')];

        $response = $this->actingAs($this->admin())
            ->post(route('paneladmin.inquiry-quotations.store'), $payload);

        $inquiry = InquiryQuotation::latest()->first();

        $response->assertRedirect(route('paneladmin.inquiry-quotations.show', $inquiry));
        $this->assertDatabaseHas('inquiry_quotation_attachments', [
            'inquiry_quotation_id' => $inquiry->id,
            'original_name' => 'dokumen.pdf',
        ]);

        $attachment = $inquiry->attachments()->first();
        Storage::disk('public')->assertExists($attachment->file_path);
    }

    public function test_invalid_file_is_rejected(): void
    {
        Storage::fake('public');

        $payload = $this->validInquiryPayload();
        $payload['attachments'] = [UploadedFile::fake()->create('virus.exe', 100)];

        $this->actingAs($this->admin())
            ->post(route('paneladmin.inquiry-quotations.store'), $payload)
            ->assertSessionHasErrors(['attachments.0']);

        $this->assertDatabaseCount('inquiry_quotations', 0);
    }

    public function test_private_inquiry_requires_at_least_one_iqm_user(): void
    {
        $payload = $this->validInquiryPayload([
            'visibility' => 'private',
            'iqm_user_ids' => [],
        ]);

        $this->actingAs($this->admin())
            ->post(route('paneladmin.inquiry-quotations.store'), $payload)
            ->assertSessionHasErrors(['iqm_user_ids']);

        $this->assertDatabaseCount('inquiry_quotations', 0);
    }

    public function test_search_client_successfully(): void
    {
        InquiryQuotation::create(array_merge($this->validInquiryPayload(), ['client_name' => 'Klien Pencarian']));
        InquiryQuotation::create(array_merge($this->validInquiryPayload(), ['client_name' => 'Klien Lain']));

        $this->actingAs($this->admin())
            ->get(route('paneladmin.inquiry-quotations.index', ['q' => 'Pencarian']))
            ->assertOk()
            ->assertSee('Klien Pencarian')
            ->assertDontSee('Klien Lain');
    }

    public function test_filter_quotation_status_successfully(): void
    {
        InquiryQuotation::create(array_merge($this->validInquiryPayload(), ['quotation_status' => 'approved', 'client_name' => 'Klien Approved']));
        InquiryQuotation::create(array_merge($this->validInquiryPayload(), ['quotation_status' => 'draft', 'client_name' => 'Klien Draft']));

        $this->actingAs($this->admin())
            ->get(route('paneladmin.inquiry-quotations.index', ['quotation_status' => 'approved']))
            ->assertOk()
            ->assertSee('Klien Approved')
            ->assertDontSee('Klien Draft');
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.com',
            'password' => bcrypt('secret'),
        ]);
    }

    private function validInquiryPayload(array $overrides = []): array
    {
        $base = [
            'inquiry_number' => 'INQ-' . now()->format('YmdHis') . '-' . mt_rand(1000, 9999),
            'inquiry_date' => now()->format('Y-m-d'),
            'inquiry_by' => 'email',
            'client_name' => 'PT Contoh Klien',
            'client_pic' => 'Budi',
            'client_phone' => '081234567890',
            'client_email' => 'client@example.com',
            'visibility' => 'public',
            'iqm_user_id' => null,
            'client_address' => 'Jl. Raya No. 1',
            'subject' => 'Permintaan Penawaran',
            'description' => 'Deskripsi proyek test.',
            'pic_internal' => 'Admin Test',
            'site_survey_status' => 'not_required',
            'site_survey_date' => null,
            'site_survey_notes' => null,
            'quotation_number' => null,
            'quotation_date' => now()->format('Y-m-d'),
            'deadline' => now()->addDays(14)->format('Y-m-d'),
            'amount' => 15000000,
            'quotation_status' => 'draft',
            'notes' => 'Catatan test inquiry.',
        ];

        return array_merge($base, $overrides);
    }
}
