<?php

namespace Tests\Feature;

use App\Mail\NewLeadMail;
use App\Models\Lead;
use App\Models\Role;
use App\Models\User;
use App\Models\WebsiteSetting;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    public function test_newsletter_and_cta_submissions_are_stored_and_email_notification_is_sent(): void
    {
        Mail::fake();
        WebsiteSetting::create([
            'nama_perusahaan' => 'Bina Persada JS',
            'email' => 'leads@binapersadajs.co.id',
        ]);

        $this->post(route('website.leads.newsletter'), [
            'source' => 'footer',
            'email' => 'subscriber@example.com',
        ])->assertRedirect()->assertSessionHas('lead_success');

        $this->post(route('website.leads.inquiry'), [
            'name' => 'PT Industri Baru',
            'email' => 'procurement@example.com',
            'phone' => '0812-1111-2222',
            'message' => 'Kami membutuhkan penawaran pekerjaan fabrikasi.',
        ])->assertRedirect()->assertSessionHas('lead_source', 'cta');

        $this->assertDatabaseHas('leads', [
            'email' => 'subscriber@example.com',
            'source' => 'footer',
            'status' => 'new',
        ]);
        $this->assertDatabaseHas('leads', [
            'email' => 'procurement@example.com',
            'source' => 'cta',
            'interest' => 'Permintaan Penawaran',
        ]);
        $this->assertDatabaseCount('activity_logs', 2);
        Mail::assertSent(NewLeadMail::class, 2);
    }

    public function test_honeypot_lead_submission_is_not_stored(): void
    {
        Mail::fake();

        $this->post(route('website.leads.newsletter'), [
            'source' => 'blog-sidebar',
            'website_url' => 'https://spam.invalid',
            'email' => 'bot@example.com',
        ])->assertRedirect()->assertSessionHas('lead_success');

        $this->post(route('website.leads.inquiry'), [
            'website_url' => 'https://spam.invalid',
            'name' => 'Bot',
            'email' => 'bot2@example.com',
        ])->assertRedirect()->assertSessionHas('lead_success');

        $this->assertDatabaseCount('leads', 0);
        Mail::assertNothingSent();
    }

    public function test_admin_can_view_update_and_delete_leads_with_activity_logs(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->userWithRole('super-admin');
        $lead = Lead::create([
            'name' => 'Lead Utama',
            'email' => 'lead@example.com',
            'phone' => '087774824737',
            'source' => 'cta',
            'status' => 'new',
        ]);

        $this->actingAs($admin)
            ->get(route('paneladmin.leads.index'))
            ->assertOk()
            ->assertSee('Lead Utama')
            ->assertSee('Minta Penawaran');

        $this->actingAs($admin)
            ->get(route('paneladmin.leads.show', $lead))
            ->assertOk()
            ->assertSee('Chat WhatsApp')
            ->assertSee('https://wa.me/6287774824737', false);

        $this->actingAs($admin)
            ->patch(route('paneladmin.leads.status', $lead), ['status' => 'qualified'])
            ->assertRedirect();
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'status' => 'qualified']);

        $this->actingAs($admin)
            ->delete(route('paneladmin.leads.destroy', $lead))
            ->assertRedirect(route('paneladmin.leads.index'));
        $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
        $this->assertDatabaseHas('activity_logs', ['module' => 'Leads', 'action' => 'update']);
        $this->assertDatabaseHas('activity_logs', ['module' => 'Leads', 'action' => 'delete']);
    }

    public function test_lead_permissions_and_dashboard_summary_follow_roles(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $content = $this->userWithRole('admin-content', 'Content Lead');
        $support = $this->userWithRole('admin-support', 'Support Lead');
        Lead::create(['email' => 'new@example.com', 'status' => 'new', 'source' => 'footer']);

        $this->actingAs($content)
            ->get(route('paneladmin.leads.index'))
            ->assertOk();
        $this->actingAs($content)
            ->patch(route('paneladmin.leads.status', Lead::first()), ['status' => 'contacted'])
            ->assertRedirect(route('paneladmin.dashboard'))
            ->assertSessionHas('error', 'Anda tidak memiliki akses.');
        $this->actingAs($support)
            ->patch(route('paneladmin.leads.status', Lead::first()), ['status' => 'contacted'])
            ->assertRedirect();

        $this->actingAs($support)
            ->get(route('paneladmin.dashboard'))
            ->assertOk()
            ->assertSee('Leads')
            ->assertSee('data-summary="total_leads" data-value="1"', false);
    }

    public function test_mail_failure_does_not_fail_lead_submission(): void
    {
        WebsiteSetting::create([
            'nama_perusahaan' => 'Bina Persada JS',
            'email' => 'notification@example.com',
        ]);

        Mail::shouldReceive('to')
            ->once()
            ->with('notification@example.com')
            ->andThrow(new \RuntimeException('SMTP gagal'));
        Log::spy();

        $this->post(route('website.leads.newsletter'), [
            'source' => 'footer',
            'email' => 'persist@example.com',
        ])->assertRedirect()->assertSessionHas('lead_success');

        $this->assertDatabaseHas('leads', ['email' => 'persist@example.com']);
        Log::shouldHaveReceived('warning')
            ->once()
            ->with('Failed to send lead notification email', \Mockery::on(function (array $context) {
                return $context['message'] === 'SMTP gagal' && ! empty($context['lead_id']);
            }));
    }

    private function userWithRole(string $slug, string $name = 'Lead Admin'): User
    {
        return User::create([
            'name' => $name,
            'email' => str_replace(' ', '.', strtolower($name)) . uniqid() . '@example.test',
            'password' => bcrypt('secret'),
            'role_id' => Role::where('slug', $slug)->firstOrFail()->id,
            'is_active' => true,
        ]);
    }
}
