<?php

namespace Tests\Feature;

use App\Models\InvoiceReport;
use App\Models\IqmUser;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InvoiceReportAttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_multiple_invoice_report_attachments(): void
    {
        Storage::fake('public');
        $this->seed(RolePermissionSeeder::class);

        $this->actingAs($this->adminUser())
            ->post(route('paneladmin.invoice-reports.store'), array_merge($this->validInvoiceReportPayload(), [
                'attachments' => [
                    UploadedFile::fake()->image('invoice-photo.jpg')->size(1024),
                    UploadedFile::fake()->create('invoice.pdf', 1024, 'application/pdf'),
                ],
            ]))
            ->assertRedirect(route('paneladmin.invoice-reports.index'));

        $invoiceReport = InvoiceReport::with('attachments')->firstOrFail();

        $this->assertCount(2, $invoiceReport->attachments);
        $invoiceReport->attachments->each(function ($attachment) {
            $this->assertStringStartsWith('invoice-reports/', $attachment->file_path);
            Storage::disk('public')->assertExists($attachment->file_path);
        });
    }

    public function test_iqm_invoice_report_attachment_download_follows_invoice_access_rules(): void
    {
        Storage::fake('public');

        $allowedUser = $this->iqmUser('allowed-invoice');
        $otherUser = $this->iqmUser('other-invoice');

        $privateReport = InvoiceReport::create($this->validInvoiceReportPayload([
            'visibility' => 'private',
            'invoice_no' => 'INV-PRIVATE',
        ]));
        $privateReport->iqmUsers()->sync([$allowedUser->id]);
        Storage::disk('public')->put('invoice-reports/private.pdf', 'private pdf');
        $privateAttachment = $privateReport->attachments()->create([
            'original_name' => 'private.pdf',
            'file_path' => 'invoice-reports/private.pdf',
            'file_type' => 'pdf',
            'file_size' => 11,
        ]);

        $publicReport = InvoiceReport::create($this->validInvoiceReportPayload([
            'visibility' => 'public',
            'invoice_no' => 'INV-PUBLIC',
        ]));
        Storage::disk('public')->put('invoice-reports/public.pdf', 'public pdf');
        $publicAttachment = $publicReport->attachments()->create([
            'original_name' => 'public.pdf',
            'file_path' => 'invoice-reports/public.pdf',
            'file_type' => 'pdf',
            'file_size' => 10,
        ]);

        $this->actingAs($allowedUser, 'iqm')
            ->get(route('iqm.invoice-report-attachments.download', $privateAttachment))
            ->assertOk();

        $this->actingAs($otherUser, 'iqm')
            ->get(route('iqm.invoice-report-attachments.download', $privateAttachment))
            ->assertForbidden();

        $this->actingAs($otherUser, 'iqm')
            ->get(route('iqm.invoice-report-attachments.download', $publicAttachment))
            ->assertOk();
    }

    public function test_admin_can_delete_selected_invoice_report_attachment_on_update(): void
    {
        Storage::fake('public');
        $this->seed(RolePermissionSeeder::class);

        $invoiceReport = InvoiceReport::create($this->validInvoiceReportPayload());
        Storage::disk('public')->put('invoice-reports/delete-me.pdf', 'delete me');
        $attachment = $invoiceReport->attachments()->create([
            'original_name' => 'delete-me.pdf',
            'file_path' => 'invoice-reports/delete-me.pdf',
            'file_type' => 'pdf',
            'file_size' => 9,
        ]);

        $this->actingAs($this->adminUser())
            ->put(route('paneladmin.invoice-reports.update', $invoiceReport), array_merge($this->validInvoiceReportPayload([
                'invoice_no' => $invoiceReport->invoice_no,
            ]), [
                'delete_attachment_ids' => [$attachment->id],
            ]))
            ->assertRedirect(route('paneladmin.invoice-reports.edit', $invoiceReport));

        $this->assertDatabaseMissing('invoice_report_attachments', ['id' => $attachment->id]);
        Storage::disk('public')->assertMissing('invoice-reports/delete-me.pdf');
    }

    public function test_invoice_report_attachment_max_file_size_is_validated(): void
    {
        Storage::fake('public');
        $this->seed(RolePermissionSeeder::class);

        $this->actingAs($this->adminUser())
            ->from(route('paneladmin.invoice-reports.create'))
            ->post(route('paneladmin.invoice-reports.store'), array_merge($this->validInvoiceReportPayload(), [
                'attachments' => [
                    UploadedFile::fake()->create('too-large.pdf', 10241, 'application/pdf'),
                ],
            ]))
            ->assertRedirect(route('paneladmin.invoice-reports.create'))
            ->assertSessionHasErrors('attachments.0');
    }

    private function adminUser(): User
    {
        return User::create([
            'name' => 'Super Admin User',
            'email' => 'super-admin-invoice@example.test',
            'password' => bcrypt('secret'),
            'role_id' => Role::where('slug', 'super-admin')->firstOrFail()->id,
            'is_active' => true,
        ]);
    }

    private function iqmUser(string $username): IqmUser
    {
        return IqmUser::create([
            'company_name' => 'PT ' . ucfirst($username),
            'pic_name' => ucfirst($username),
            'username' => $username,
            'email' => $username . '@example.test',
            'password' => 'password',
            'status' => 'active',
        ]);
    }

    private function validInvoiceReportPayload(array $overrides = []): array
    {
        return array_merge([
            'client' => 'PT Contoh',
            'invoice_no' => 'INV-' . uniqid(),
            'po_wo_no' => 'PO-001',
            'job_title' => 'Invoice Report Test',
            'invoice_date' => now()->format('Y-m-d'),
            'quantity' => '2',
            'unit' => 'pcs',
            'unit_price' => '1000000',
            'total_amount' => '2000000',
            'visibility' => 'public',
            'sort_order' => 0,
            'is_active' => '1',
        ], $overrides);
    }
}
