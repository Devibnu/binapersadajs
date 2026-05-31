<?php

namespace Tests\Feature;

use App\Models\IqmUser;
use App\Models\InquiryQuotation;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IqmAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_iqm_users_admin_page_supports_create_edit_view_and_delete(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->adminUser();
        $iqmUser = IqmUser::create([
            'company_name' => 'PT Client',
            'pic_name' => 'Client PIC',
            'username' => 'client@example.test',
            'email' => 'client@example.test',
            'password' => '123456',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('paneladmin.iqm-users.index'))
            ->assertOk()
            ->assertSee('+ Tambah User IQM')
            ->assertSee('Detail')
            ->assertSee('Edit')
            ->assertSee('Hapus')
            ->assertDontSee('Nonaktifkan')
            ->assertDontSee('Aktifkan');

        $this->actingAs($admin)
            ->get(route('paneladmin.iqm-users.show', $iqmUser))
            ->assertOk()
            ->assertSee('PT Client');

        $this->actingAs($admin)
            ->get(route('paneladmin.iqm-users.create'))
            ->assertOk()
            ->assertSee('Tambah User Portal IQM');

        $this->actingAs($admin)
            ->post(route('paneladmin.iqm-users.store'), [
                'company_name' => 'PT New Client',
                'pic_name' => 'New PIC',
                'username' => 'new-client@example.test',
                'email' => 'new-client@example.test',
                'phone' => '08123456789',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'status' => 'active',
            ])
            ->assertRedirect(route('paneladmin.iqm-users.index'));

        $created = IqmUser::where('username', 'new-client@example.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('paneladmin.iqm-users.edit', $created))
            ->assertOk()
            ->assertSee('Edit User Portal IQM')
            ->assertSee('kosongkan jika tidak diubah');

        $this->actingAs($admin)
            ->put(route('paneladmin.iqm-users.update', $created), [
                'company_name' => 'PT Client Updated',
                'pic_name' => 'Updated PIC',
                'username' => 'new-client@example.test',
                'email' => 'new-client@example.test',
                'phone' => '08987654321',
                'password' => '',
                'password_confirmation' => '',
                'status' => 'inactive',
            ])
            ->assertRedirect(route('paneladmin.iqm-users.index'));

        $this->assertDatabaseHas('iqm_users', [
            'id' => $created->id,
            'company_name' => 'PT Client Updated',
            'status' => 'inactive',
        ]);
    }

    public function test_iqm_login_redirects_to_iqm_root_and_logout_returns_to_login(): void
    {
        $iqmUser = IqmUser::create([
            'company_name' => 'Administrator',
            'pic_name' => 'Administrator',
            'username' => 'admin@softui.com',
            'email' => 'admin@softui.com',
            'password' => '123456',
            'status' => 'active',
        ]);

        $this->get(route('iqm.landing'))
            ->assertRedirect(route('iqm.login'));

        $this->post(route('iqm.authenticate'), [
            'username' => 'admin@softui.com',
            'password' => '123456',
        ])->assertRedirect(route('iqm.landing'));

        $this->post(route('iqm.logout'))
            ->assertRedirect(route('iqm.login'));

        $this->actingAs($iqmUser, 'iqm')
            ->get(route('iqm.login'))
            ->assertRedirect(route('iqm.landing'));
    }

    public function test_iqm_login_accepts_username_or_email_and_rejects_invalid_credentials(): void
    {
        IqmUser::create([
            'company_name' => 'PT Indah',
            'pic_name' => 'Indah',
            'username' => 'indah',
            'email' => 'indah@gmail.com',
            'password' => '123456',
            'status' => 'active',
        ]);
        IqmUser::create([
            'company_name' => 'PT Inactive',
            'pic_name' => 'Inactive PIC',
            'username' => 'inactive',
            'email' => 'inactive@example.test',
            'password' => '123456',
            'status' => 'inactive',
        ]);

        $this->post(route('iqm.authenticate'), [
            'username' => 'indah',
            'password' => '123456',
        ])->assertRedirect(route('iqm.landing'));

        $this->post(route('iqm.logout'))->assertRedirect(route('iqm.login'));

        $this->post(route('iqm.authenticate'), [
            'username' => 'indah@gmail.com',
            'password' => '123456',
        ])->assertRedirect(route('iqm.landing'));

        $this->post(route('iqm.logout'))->assertRedirect(route('iqm.login'));

        $this->from(route('iqm.login'))
            ->post(route('iqm.authenticate'), [
                'username' => 'indah',
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('iqm.login'))
            ->assertSessionHasErrors('username');

        $this->from(route('iqm.login'))
            ->post(route('iqm.authenticate'), [
                'username' => 'inactive',
                'password' => '123456',
            ])
            ->assertRedirect(route('iqm.login'))
            ->assertSessionHasErrors('username');
    }

    public function test_iqm_user_update_keeps_old_password_and_phone_when_left_blank_or_missing(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->adminUser();
        $iqmUser = IqmUser::create([
            'company_name' => 'PT Keep Data',
            'pic_name' => 'Keep PIC',
            'username' => 'keep-data',
            'email' => 'keep-data@example.test',
            'phone' => '081111111111',
            'password' => 'old-password',
            'status' => 'active',
        ]);
        $oldPasswordHash = $iqmUser->password;

        $this->actingAs($admin)
            ->put(route('paneladmin.iqm-users.update', $iqmUser), [
                'company_name' => 'PT Keep Data Updated',
                'pic_name' => 'Keep PIC Updated',
                'username' => 'keep-data',
                'email' => 'keep-data@example.test',
                'password' => '',
                'password_confirmation' => '',
                'status' => 'active',
            ])
            ->assertRedirect(route('paneladmin.iqm-users.index'));

        $iqmUser->refresh();

        $this->assertSame('081111111111', $iqmUser->phone);
        $this->assertSame($oldPasswordHash, $iqmUser->password);
        $this->assertTrue(Hash::check('old-password', $iqmUser->password));

        $this->actingAs($admin)
            ->get(route('paneladmin.iqm-users.edit', $iqmUser))
            ->assertOk()
            ->assertSee('081111111111');
    }

    public function test_iqm_portal_only_shows_inquiries_assigned_to_logged_in_iqm_user(): void
    {
        $indah = IqmUser::create([
            'company_name' => 'PT Indah',
            'pic_name' => 'Indah',
            'username' => 'indah',
            'email' => 'indah@gmail.com',
            'password' => '123456',
            'status' => 'active',
        ]);
        $otherUser = IqmUser::create([
            'company_name' => 'PT Lain',
            'pic_name' => 'User Lain',
            'username' => 'lain',
            'email' => 'lain@example.test',
            'password' => '123456',
            'status' => 'active',
        ]);
        $indahInquiry = InquiryQuotation::create($this->validInquiryPayload([
            'iqm_user_id' => $indah->id,
            'visibility' => 'private',
            'client_name' => 'PT Client Indah',
            'subject' => 'Inquiry Milik Indah',
        ]));
        $indahInquiry->iqmUsers()->sync([$indah->id]);
        $otherInquiry = InquiryQuotation::create($this->validInquiryPayload([
            'iqm_user_id' => $otherUser->id,
            'visibility' => 'private',
            'client_name' => 'PT Client Lain',
            'subject' => 'Inquiry Milik User Lain',
        ]));
        $otherInquiry->iqmUsers()->sync([$otherUser->id]);
        $sharedInquiry = InquiryQuotation::create($this->validInquiryPayload([
            'iqm_user_id' => $indah->id,
            'visibility' => 'private',
            'client_name' => 'PT Shared',
            'subject' => 'Inquiry Dua User',
        ]));
        $sharedInquiry->iqmUsers()->sync([$indah->id, $otherUser->id]);
        $publicInquiry = InquiryQuotation::create($this->validInquiryPayload([
            'iqm_user_id' => null,
            'visibility' => 'public',
            'client_name' => 'PT Public',
            'subject' => 'Inquiry Public',
        ]));
        InquiryQuotation::create($this->validInquiryPayload([
            'iqm_user_id' => null,
            'visibility' => 'private',
            'client_name' => 'PT Belum Dibagikan',
            'subject' => 'Inquiry Belum Dibagikan',
        ]));

        $this->actingAs($indah, 'iqm')
            ->get(route('iqm.landing'))
            ->assertOk()
            ->assertSee('Inquiry Milik Indah')
            ->assertSee('Inquiry Dua User')
            ->assertSee('Inquiry Public')
            ->assertDontSee('Inquiry Milik User Lain')
            ->assertDontSee('Inquiry Belum Dibagikan');

        $this->actingAs($otherUser, 'iqm')
            ->get(route('iqm.landing'))
            ->assertOk()
            ->assertSee('Inquiry Dua User')
            ->assertSee('Inquiry Public')
            ->assertSee('Inquiry Milik User Lain')
            ->assertDontSee('Inquiry Milik Indah');

        $this->actingAs($indah, 'iqm')
            ->get(route('iqm.inquiries.show', $indahInquiry))
            ->assertOk();

        $this->actingAs($indah, 'iqm')
            ->get(route('iqm.inquiries.show', $publicInquiry))
            ->assertOk();

        $this->actingAs($indah, 'iqm')
            ->get(route('iqm.inquiries.show', $otherInquiry))
            ->assertForbidden();
    }

    private function adminUser(): User
    {
        return User::create([
            'name' => 'Super Admin User',
            'email' => 'super-admin@example.test',
            'password' => bcrypt('secret'),
            'role_id' => Role::where('slug', 'super-admin')->firstOrFail()->id,
            'is_active' => true,
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
