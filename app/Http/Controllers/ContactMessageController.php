<?php

namespace App\Http\Controllers;

use App\Mail\NewContactMessageMail;
use App\Models\ContactMessage;
use App\Models\ContactPageSetting;
use App\Models\EmailSetting;
use App\Models\WebsiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ContactMessageController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $message = ContactPageSetting::current()->success_message;

        if ($request->filled('website_url')) {
            return back()->with('contact_success', $message);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $contactMessage = ContactMessage::create([
            ...$validated,
            'status' => 'unread',
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000, '') ?: null,
        ]);

        EmailSetting::applyActiveConfiguration();
        $websiteSetting = WebsiteSetting::query()->first();
        $recipient = $websiteSetting?->email ?: config('mail.from.address');

        if ($recipient) {
            try {
                Mail::to($recipient)->send(new NewContactMessageMail($contactMessage, $websiteSetting));
            } catch (\Throwable $exception) {
                Log::warning('Failed to send contact notification email', [
                    'contact_message_id' => $contactMessage->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return back()->with('contact_success', $message);
    }
}
