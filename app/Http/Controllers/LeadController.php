<?php

namespace App\Http\Controllers;

use App\Mail\NewLeadMail;
use App\Models\EmailSetting;
use App\Models\Lead;
use App\Models\WebsiteSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    public function storeNewsletter(Request $request): RedirectResponse
    {
        $source = in_array($request->input('source'), ['footer', 'blog-sidebar'], true)
            ? $request->input('source')
            : 'footer';
        $message = 'Terima kasih. Anda telah berlangganan update kami.';

        if ($request->filled('website_url')) {
            return back()->with(['lead_success' => $message, 'lead_source' => $source]);
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:150'],
            'source' => ['nullable', Rule::in(['footer', 'blog-sidebar'])],
        ]);

        $lead = Lead::create([
            'email' => $validated['email'],
            'interest' => 'Newsletter Update',
            'source' => $source,
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000, '') ?: null,
        ]);

        $this->afterStored($lead);

        return back()->with(['lead_success' => $message, 'lead_source' => $source]);
    }

    public function storeInquiry(Request $request): RedirectResponse
    {
        $message = 'Permintaan Anda berhasil dikirim. Tim kami akan segera menghubungi Anda.';

        if ($request->filled('website_url')) {
            return back()->with(['lead_success' => $message, 'lead_source' => 'cta']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'company' => ['nullable', 'string', 'max:150'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $lead = Lead::create([
            ...$validated,
            'interest' => 'Permintaan Penawaran',
            'source' => 'cta',
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000, '') ?: null,
        ]);

        $this->afterStored($lead);

        return back()->with(['lead_success' => $message, 'lead_source' => 'cta']);
    }

    private function afterStored(Lead $lead): void
    {
        app(ActivityLogger::class)->log(
            'create',
            'Leads',
            'Lead baru diterima dari ' . $lead->sourceLabel(),
            $lead,
            ['source' => $lead->source]
        );

        EmailSetting::applyActiveConfiguration();
        $websiteSetting = WebsiteSetting::query()->first();
        $recipient = $websiteSetting?->email ?: config('mail.from.address');

        if (! $recipient) {
            return;
        }

        try {
            Mail::to($recipient)->send(new NewLeadMail($lead, $websiteSetting));
        } catch (\Throwable $exception) {
            Log::warning('Failed to send lead notification email', [
                'lead_id' => $lead->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
