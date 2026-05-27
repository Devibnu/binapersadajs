<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageReplyMail;
use App\Models\ContactMessage;
use App\Models\EmailSetting;
use App\Models\WebsiteSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $status = in_array($request->query('status'), ['unread', 'read', 'replied'], true)
            ? $request->query('status')
            : null;
        $search = trim((string) $request->query('q'));

        $messages = ContactMessage::query()
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('subject', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->get();

        $counts = ContactMessage::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('paneladmin.contact-messages.index', compact('messages', 'counts', 'status', 'search'));
    }

    public function show(ContactMessage $contactMessage): View
    {
        $contactMessage->load(['replies.sender']);

        return view('paneladmin.contact-messages.show', compact('contactMessage'));
    }

    public function markRead(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->update(['status' => 'read']);
        app(ActivityLogger::class)->log('mark_read', 'Contact Messages', 'Pesan ditandai sudah dibaca dari ' . $contactMessage->name, $contactMessage);

        return back()->with('success', 'Pesan berhasil ditandai sudah dibaca.');
    }

    public function markReplied(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->update(['status' => 'replied']);
        app(ActivityLogger::class)->log('mark_replied', 'Contact Messages', 'Pesan ditandai sudah dibalas dari ' . $contactMessage->name, $contactMessage);

        return back()->with('success', 'Pesan berhasil ditandai sudah dibalas.');
    }

    public function sendReply(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $validated = $request->validate([
            'to_email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'min:10'],
        ]);

        try {
            EmailSetting::applyActiveConfiguration();
            Mail::to($validated['to_email'])->send(new ContactMessageReplyMail(
                $contactMessage,
                $validated['subject'],
                $validated['body'],
                WebsiteSetting::query()->first()
            ));
        } catch (\Throwable $exception) {
            Log::warning('Failed to send contact reply email', [
                'contact_message_id' => $contactMessage->id,
                'message' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Balasan email gagal dikirim. Periksa konfigurasi SMTP.');
        }

        $contactMessage->replies()->create([
            'to_email' => $validated['to_email'],
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'sent_by' => $request->user()?->id,
            'sent_at' => now(),
        ]);
        $contactMessage->update(['status' => 'replied']);
        app(ActivityLogger::class)->log('email', 'Contact Messages', 'Balasan email dikirim kepada ' . $contactMessage->name, $contactMessage, [
            'to_email' => $validated['to_email'],
            'subject' => $validated['subject'],
        ]);

        return back()->with('success', 'Balasan email berhasil dikirim.');
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        app(ActivityLogger::class)->log('delete', 'Contact Messages', 'Pesan kontak dihapus dari ' . $contactMessage->name, $contactMessage);
        $contactMessage->delete();

        return redirect()
            ->route('paneladmin.contact-messages.index')
            ->with('success', 'Pesan kontak berhasil dihapus.');
    }
}
