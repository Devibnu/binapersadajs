<?php

namespace Tests\Feature;

use App\Models\IqmUser;
use App\Models\ProjectReport;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectReportAttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_multiple_project_report_attachments(): void
    {
        Storage::fake('local');
        $this->seed(RolePermissionSeeder::class);

        $this->actingAs($this->adminUser())
            ->post(route('paneladmin.project-reports.store'), array_merge($this->validProjectReportPayload(), [
                'attachments' => [
                    UploadedFile::fake()->image('progress.jpg')->size(1024),
                    UploadedFile::fake()->create('dokumen.pdf', 1024, 'application/pdf'),
                ],
            ]))
            ->assertRedirect(route('paneladmin.project-reports.index'));

        $projectReport = ProjectReport::with('attachments')->firstOrFail();

        $this->assertCount(2, $projectReport->attachments);
        $projectReport->attachments->each(function ($attachment) {
            $this->assertStringStartsWith('project-reports/', $attachment->file_path);
            Storage::disk('local')->assertExists($attachment->file_path);
        });
    }

    public function test_iqm_project_report_attachment_download_follows_project_access_rules(): void
    {
        Storage::fake('local');

        $allowedUser = $this->iqmUser('allowed');
        $otherUser = $this->iqmUser('other');

        $privateReport = ProjectReport::create($this->validProjectReportPayload([
            'visibility' => 'private',
            'job_title' => 'Private Project',
        ]));
        $privateReport->iqmUsers()->sync([$allowedUser->id]);
        Storage::disk('local')->put('project-reports/private.pdf', 'private pdf');
        $privateAttachment = $privateReport->attachments()->create([
            'file_name' => 'private.pdf',
            'original_name' => 'private.pdf',
            'file_path' => 'project-reports/private.pdf',
            'file_type' => 'pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 11,
        ]);

        $publicReport = ProjectReport::create($this->validProjectReportPayload([
            'visibility' => 'public',
            'job_title' => 'Public Project',
        ]));
        Storage::disk('local')->put('project-reports/public.pdf', 'public pdf');
        $publicAttachment = $publicReport->attachments()->create([
            'file_name' => 'public.pdf',
            'original_name' => 'public.pdf',
            'file_path' => 'project-reports/public.pdf',
            'file_type' => 'pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 10,
        ]);

        $this->actingAs($allowedUser, 'iqm')
            ->get(route('iqm.project-report-attachments.download', $privateAttachment))
            ->assertOk();

        $this->actingAs($otherUser, 'iqm')
            ->get(route('iqm.project-report-attachments.download', $privateAttachment))
            ->assertForbidden();

        $this->actingAs($otherUser, 'iqm')
            ->get(route('iqm.project-report-attachments.download', $publicAttachment))
            ->assertOk();
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

    private function validProjectReportPayload(array $overrides = []): array
    {
        return array_merge([
            'project_no' => 'PRJ-' . uniqid(),
            'job_title' => 'Project Report Test',
            'quotation_price' => '1000000',
            'contract_number' => 'CTR-001',
            'contract_price' => '2000000',
            'invoice_amount' => '1500000',
            'corporation' => 'PT Contoh',
            'department' => 'Engineering',
            'user_pic' => 'Budi',
            'remark' => 'Progress project test.',
            'e_wo_status' => 'Open',
            'report_status' => 'On Progress',
            'visibility' => 'public',
            'sort_order' => 0,
            'is_active' => '1',
        ], $overrides);
    }
}
