<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BrandedTemplateMail;
use App\Models\EmailSetting;
use App\Models\EmailTemplate;
use App\Models\Lead;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $status = in_array($request->query('status'), ['new', 'contacted', 'qualified', 'converted', 'closed'], true)
            ? $request->query('status')
            : null;
        $source = filled($request->query('source')) ? (string) $request->query('source') : null;

        $leads = Lead::query()
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($source, fn ($query) => $query->where('source', $source))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('company', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = Lead::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $sources = Lead::query()->whereNotNull('source')->distinct()->orderBy('source')->pluck('source');

        return view('paneladmin.leads.index', compact('leads', 'counts', 'sources', 'search', 'status', 'source'));
    }

    public function show(Lead $lead): View
    {
        $lead->load(['emailHistories.sender']);

        return view('paneladmin.leads.show', compact('lead'));
    }

    public function sendEmail(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'to_email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'min:10'],
        ]);

        $emailSetting = EmailSetting::active();
        if (! $emailSetting) {
            return back()->withInput()->with('error', 'Aktifkan dan simpan konfigurasi SMTP terlebih dahulu.');
        }

        try {
            $emailSetting->applyConfiguration();
            Mail::to($validated['to_email'])->send(new BrandedTemplateMail(
                $validated['subject'],
                '<p style="margin:0 0 20px;">Yth. ' . e($lead->name ?: 'Bapak/Ibu') . ',</p><div>' . nl2br(e($validated['body'])) . '</div>',
                EmailTemplate::current()
            ));
        } catch (\Throwable $exception) {
            Log::warning('Failed to send lead follow up email', [
                'lead_id' => $lead->id,
                'to' => $validated['to_email'],
                'message' => $exception->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Email follow up gagal dikirim. Periksa konfigurasi SMTP.');
        }

        $lead->emailHistories()->create([
            'user_id' => $request->user()?->id,
            'to_email' => $validated['to_email'],
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'sent_at' => now(),
        ]);

        app(ActivityLogger::class)->log('email', 'Leads', 'Admin mengirim email follow up kepada Lead: ' . ($lead->name ?: $lead->email), $lead, [
            'to_email' => $validated['to_email'],
            'subject' => $validated['subject'],
        ]);

        return back()->with('success', 'Email follow up berhasil dikirim.');
    }

    public function updateStatus(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['new', 'contacted', 'qualified', 'converted', 'closed'])],
        ]);

        $oldStatus = $lead->status;
        $lead->update($validated);
        app(ActivityLogger::class)->log('update', 'Leads', 'Status lead diperbarui: ' . $lead->email, $lead, [
            'status_from' => $oldStatus,
            'status_to' => $lead->status,
        ]);

        return back()->with('success', 'Status lead berhasil diperbarui.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        app(ActivityLogger::class)->log('delete', 'Leads', 'Lead dihapus: ' . $lead->email, $lead);
        $lead->delete();

        return redirect()->route('paneladmin.leads.index')->with('success', 'Lead berhasil dihapus.');
    }
}
