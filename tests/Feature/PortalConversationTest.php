<?php

namespace Tests\Feature;

use App\Models\InquiryQuotation;
use App\Models\IqmUser;
use App\Models\PortalConversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalConversationTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_send_message_only_for_visible_inquiry(): void
    {
        $client = $this->iqmUser('client@example.test');
        $otherClient = $this->iqmUser('other@example.test');
        $visibleInquiry = InquiryQuotation::create($this->validInquiryPayload([
            'visibility' => 'private',
            'subject' => 'Visible Inquiry',
        ]));
        $visibleInquiry->iqmUsers()->sync([$client->id]);
        $hiddenInquiry = InquiryQuotation::create($this->validInquiryPayload([
            'visibility' => 'private',
            'subject' => 'Hidden Inquiry',
        ]));
        $hiddenInquiry->iqmUsers()->sync([$otherClient->id]);

        $this->actingAs($client, 'iqm')
            ->post(route('iqm.inquiries.conversations.store', $visibleInquiry), [
                'message' => 'Apakah quotation ini masih dapat direvisi?',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('portal_conversations', [
            'module_type' => PortalConversation::MODULE_INQUIRY,
            'module_id' => $visibleInquiry->id,
            'sender_type' => 'client',
            'sender_id' => $client->id,
            'message' => 'Apakah quotation ini masih dapat direvisi?',
            'is_read' => false,
        ]);

        $this->actingAs($client, 'iqm')
            ->post(route('iqm.inquiries.conversations.store', $hiddenInquiry), [
                'message' => 'Tidak boleh terkirim.',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('portal_conversations', [
            'module_id' => $hiddenInquiry->id,
            'message' => 'Tidak boleh terkirim.',
        ]);
    }

    public function test_admin_can_reply_and_client_detail_marks_admin_message_read(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => bcrypt('secret'),
            'is_active' => true,
        ]);
        $client = $this->iqmUser('client@example.test');
        $inquiry = InquiryQuotation::create($this->validInquiryPayload([
            'visibility' => 'private',
            'subject' => 'Inquiry Chat',
        ]));
        $inquiry->iqmUsers()->sync([$client->id]);

        $this->actingAs($admin)
            ->post(route('paneladmin.inquiry-quotations.conversations.store', $inquiry), [
                'message' => 'Ya, masih dapat direvisi sesuai kebutuhan.',
            ])
            ->assertRedirect();

        $message = PortalConversation::firstOrFail();
        $this->assertFalse($message->is_read);

        $this->actingAs($client, 'iqm')
            ->get(route('iqm.inquiries.show', $inquiry))
            ->assertOk()
            ->assertSee('Ya, masih dapat direvisi sesuai kebutuhan.');

        $this->assertTrue($message->fresh()->is_read);
    }

    private function iqmUser(string $email): IqmUser
    {
        return IqmUser::create([
            'company_name' => 'PT ' . ucfirst(strtok($email, '@')),
            'pic_name' => 'Client PIC',
            'username' => $email,
            'email' => $email,
            'password' => '123456',
            'status' => 'active',
        ]);
    }

    private function validInquiryPayload(array $overrides = []): array
    {
        return array_merge([
            'inquiry_number' => 'INQ-' . uniqid(),
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
        ], $overrides);
    }
}
