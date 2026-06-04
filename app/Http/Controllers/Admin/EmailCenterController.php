<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BrandedTemplateMail;
use App\Models\EmailAccount;
use App\Models\EmailCenterMessage;
use App\Models\EmailTemplate;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmailCenterController extends Controller
{
    public function index(Request $request): View
    {
        $accounts = EmailAccount::query()->orderByDesc('is_active')->orderBy('name')->get();
        $account = $this->selectedAccount($request, $accounts);
        $folder = $request->query('folder', 'inbox');
        $search = trim((string) $request->query('q'));
        $imapNotice = null;
        $messages = collect();
        $selectedMessage = null;

        if ($folder === 'inbox') {
            [$messages, $imapNotice] = $account ? $this->imapInbox($account, $search) : [collect(), 'Tambahkan Email Account aktif terlebih dahulu.'];
        } else {
            $query = EmailCenterMessage::query()
                ->with(['account', 'attachments'])
                ->where('folder', $folder)
                ->when($account, fn ($query) => $query->where('email_account_id', $account->id))
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('to_email', 'like', '%' . $search . '%')
                            ->orWhere('from_email', 'like', '%' . $search . '%')
                            ->orWhere('subject', 'like', '%' . $search . '%')
                            ->orWhere('body', 'like', '%' . $search . '%');
                    });
                })
                ->latest();

            $messages = $query->paginate(20)->withQueryString();
            $selectedMessage = EmailCenterMessage::query()
                ->with(['account', 'attachments'])
                ->where('folder', $folder)
                ->when($account, fn ($query) => $query->where('email_account_id', $account->id))
                ->find($request->query('message')) ?: $messages->first();
        }

        return view('paneladmin.email-center.index', compact('accounts', 'account', 'folder', 'search', 'messages', 'selectedMessage', 'imapNotice'));
    }

    public function compose(Request $request): View
    {
        return view('paneladmin.email-center.compose', [
            'accounts' => EmailAccount::query()->where('is_active', true)->orderBy('name')->get(),
            'draft' => null,
            'prefill' => [
                'to_email' => $request->query('to'),
                'subject' => $request->query('subject'),
                'body' => $request->query('body'),
                'action_type' => $request->query('action_type', 'send'),
            ],
        ]);
    }

    public function editDraft(EmailCenterMessage $message): View
    {
        abort_unless($message->folder === 'draft', 404);

        return view('paneladmin.email-center.compose', [
            'accounts' => EmailAccount::query()->where('is_active', true)->orderBy('name')->get(),
            'draft' => $message->load('attachments'),
            'prefill' => [],
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $validated = $this->validatedMessage($request);
        $account = EmailAccount::query()->where('is_active', true)->findOrFail($validated['email_account_id']);
        $message = $this->storeMessage($request, $validated, 'sent', 'sent');

        try {
            $account->applySmtpConfiguration();
            Mail::to($this->emailList($validated['to_email']))
                ->cc($this->emailList($validated['cc'] ?? ''))
                ->bcc($this->emailList($validated['bcc'] ?? ''))
                ->send($this->mailableFor($message));
        } catch (\Throwable $exception) {
            $message->update(['status' => 'failed', 'folder' => 'sent']);
            Log::warning('Email Center send failed', [
                'message_id' => $message->id,
                'error' => $exception->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Email gagal dikirim. Periksa SMTP account dan alamat tujuan.');
        }

        $message->update(['status' => 'sent', 'sent_at' => now()]);
        $action = match ($request->input('action_type')) {
            'reply' => 'reply_email',
            'forward' => 'forward_email',
            default => 'send_email',
        };
        $description = match ($action) {
            'reply_email' => 'Admin membalas email ke ' . $message->to_email,
            'forward_email' => 'Admin meneruskan email ke ' . $message->to_email,
            default => 'Admin mengirim email ke ' . $message->to_email,
        };

        app(ActivityLogger::class)->log($action, 'Email Center', $description, $message, [
            'to_email' => $message->to_email,
            'subject' => $message->subject,
        ]);

        return redirect()->route('paneladmin.email-center.index', ['folder' => 'sent', 'account_id' => $account->id])
            ->with('success', 'Email berhasil dikirim.');
    }

    public function saveDraft(Request $request): RedirectResponse
    {
        $validated = $this->validatedMessage($request, false);
        $message = $this->storeMessage($request, $validated, 'draft', 'draft');

        app(ActivityLogger::class)->log('save_draft', 'Email Center', 'Admin menyimpan draft email: ' . ($message->subject ?: '(tanpa subject)'), $message);

        return redirect()->route('paneladmin.email-center.index', ['folder' => 'draft'])
            ->with('success', 'Draft berhasil disimpan.');
    }

    public function deleteMessage(EmailCenterMessage $message): RedirectResponse
    {
        $message->update(['folder' => 'trash', 'status' => 'trash']);
        app(ActivityLogger::class)->log('delete_email', 'Email Center', 'Admin menghapus email: ' . ($message->subject ?: '-'), $message);

        return back()->with('success', 'Email dipindahkan ke Trash.');
    }

    public function restoreMessage(EmailCenterMessage $message): RedirectResponse
    {
        abort_unless($message->folder === 'trash', 404);
        $message->update(['folder' => $message->sent_at ? 'sent' : 'draft', 'status' => $message->sent_at ? 'sent' : 'draft']);
        app(ActivityLogger::class)->log('restore_email', 'Email Center', 'Admin memulihkan email: ' . ($message->subject ?: '-'), $message);

        return back()->with('success', 'Email berhasil dipulihkan.');
    }

    public function forceDeleteMessage(EmailCenterMessage $message): RedirectResponse
    {
        abort_unless($message->folder === 'trash', 404);
        $message->attachments->each(fn ($attachment) => Storage::disk('public')->delete($attachment->file_path));
        $message->delete();

        return redirect()->route('paneladmin.email-center.index', ['folder' => 'trash'])->with('success', 'Email dihapus permanen.');
    }

    public function imapAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'account_id' => ['required', 'exists:email_accounts,id'],
            'uid' => ['required', 'integer'],
            'action' => ['required', Rule::in(['read', 'unread', 'delete'])],
        ]);
        $account = EmailAccount::findOrFail($validated['account_id']);

        if (! function_exists('imap_open')) {
            return back()->with('error', 'Extension PHP IMAP belum aktif di server.');
        }

        $imap = @imap_open($account->imapMailbox(), $account->imap_username, $account->imap_password);
        if (! $imap) {
            return back()->with('error', 'Gagal terhubung ke IMAP account.');
        }

        match ($validated['action']) {
            'read' => imap_setflag_full($imap, (string) $validated['uid'], '\\Seen', ST_UID),
            'unread' => imap_clearflag_full($imap, (string) $validated['uid'], '\\Seen', ST_UID),
            'delete' => imap_delete($imap, (string) $validated['uid'], FT_UID),
        };
        if ($validated['action'] === 'delete') {
            imap_expunge($imap);
        }
        imap_close($imap);

        app(ActivityLogger::class)->log($validated['action'] === 'delete' ? 'delete_email' : 'status_change', 'Email Center', 'Admin memperbarui email inbox UID: ' . $validated['uid']);

        return back()->with('success', 'Status email inbox berhasil diperbarui.');
    }

    public function accounts(): View
    {
        return view('paneladmin.email-center.accounts', [
            'accounts' => EmailAccount::query()->latest()->get(),
            'account' => new EmailAccount(['is_active' => true, 'smtp_port' => 587, 'smtp_encryption' => 'tls', 'imap_port' => 993, 'imap_encryption' => 'ssl']),
        ]);
    }

    public function storeAccount(Request $request): RedirectResponse
    {
        $account = EmailAccount::create($this->validatedAccount($request));
        app(ActivityLogger::class)->log('login_account', 'Email Center', 'Admin menambahkan email account: ' . $account->email, $account);

        return back()->with('success', 'Email account berhasil ditambahkan.');
    }

    public function updateAccount(Request $request, EmailAccount $account): RedirectResponse
    {
        $validated = $this->validatedAccount($request, $account);
        foreach (['smtp_password', 'imap_password'] as $passwordField) {
            if (! filled($validated[$passwordField] ?? null)) {
                unset($validated[$passwordField]);
            }
        }
        $account->update($validated);

        return back()->with('success', 'Email account berhasil diperbarui.');
    }

    public function destroyAccount(EmailAccount $account): RedirectResponse
    {
        $account->delete();

        return back()->with('success', 'Email account berhasil dihapus.');
    }

    private function selectedAccount(Request $request, $accounts): ?EmailAccount
    {
        return $accounts->firstWhere('id', (int) $request->query('account_id'))
            ?: $accounts->firstWhere('is_active', true)
            ?: $accounts->first();
    }

    private function imapInbox(EmailAccount $account, string $search): array
    {
        if (! function_exists('imap_open')) {
            return [collect(), 'Extension PHP IMAP belum aktif. Aktifkan extension imap di server untuk membaca Inbox.'];
        }

        if (! filled($account->imap_host) || ! filled($account->imap_username) || ! filled($account->imap_password)) {
            return [collect(), 'Lengkapi konfigurasi IMAP pada Email Account.'];
        }

        $imap = @imap_open($account->imapMailbox(), $account->imap_username, $account->imap_password);
        if (! $imap) {
            return [collect(), 'Gagal terhubung ke IMAP: ' . imap_last_error()];
        }

        $criteria = $search !== '' ? 'TEXT "' . addslashes($search) . '"' : 'ALL';
        $uids = imap_search($imap, $criteria, SE_UID) ?: [];
        rsort($uids);
        $messages = collect(array_slice($uids, 0, 50))->map(function ($uid) use ($imap) {
            $overview = imap_fetch_overview($imap, (string) $uid, FT_UID)[0] ?? null;
            $body = quoted_printable_decode((string) imap_fetchbody($imap, (string) $uid, '1', FT_UID));

            return (object) [
                'uid' => $uid,
                'from' => $overview?->from ?? '-',
                'subject' => imap_utf8($overview?->subject ?? '(tanpa subject)'),
                'date' => isset($overview->date) ? date('d/m/Y H:i', strtotime($overview->date)) : '-',
                'body' => str(strip_tags($body))->squish()->toString(),
                'preview' => str(strip_tags($body))->squish()->limit(160)->toString(),
                'seen' => (bool) ($overview?->seen ?? false),
                'has_attachment' => false,
            ];
        });
        imap_close($imap);

        return [$messages, null];
    }

    private function validatedMessage(Request $request, bool $strict = true): array
    {
        return $request->validate([
            'email_account_id' => ['required', 'exists:email_accounts,id'],
            'to_email' => [$strict ? 'required' : 'nullable', 'string', 'max:1000'],
            'cc' => ['nullable', 'string', 'max:1000'],
            'bcc' => ['nullable', 'string', 'max:1000'],
            'subject' => [$strict ? 'required' : 'nullable', 'string', 'max:255'],
            'body' => [$strict ? 'required' : 'nullable', 'string'],
            'use_template' => ['nullable', 'boolean'],
            'action_type' => ['nullable', Rule::in(['send', 'reply', 'forward'])],
            'attachments.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,zip,jpg,jpeg,png', 'max:10240'],
        ]);
    }

    private function storeMessage(Request $request, array $validated, string $folder, string $status): EmailCenterMessage
    {
        $account = EmailAccount::find($validated['email_account_id']);
        $message = EmailCenterMessage::updateOrCreate(
            ['id' => $request->input('draft_id')],
            [
                'email_account_id' => $account?->id,
                'user_id' => $request->user()?->id,
                'folder' => $folder,
                'from_email' => $account?->email,
                'to_email' => $validated['to_email'] ?? null,
                'cc' => $validated['cc'] ?? null,
                'bcc' => $validated['bcc'] ?? null,
                'subject' => $validated['subject'] ?? null,
                'body' => $validated['body'] ?? null,
                'use_template' => $request->boolean('use_template', true),
                'status' => $status,
            ]
        );

        foreach ($request->file('attachments', []) as $file) {
            $message->attachments()->create([
                'file_path' => $file->store('email-center', 'public'),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        return $message->load('attachments');
    }

    private function mailableFor(EmailCenterMessage $message): BrandedTemplateMail
    {
        $body = $message->use_template
            ? nl2br(e($message->body))
            : '<div style="font-family:Arial,sans-serif;line-height:1.7;">' . nl2br(e($message->body)) . '</div>';

        return new BrandedTemplateMail(
            $message->subject ?: '(tanpa subject)',
            $body,
            $message->use_template ? EmailTemplate::current() : null,
            [],
            $message->attachments->map(fn ($attachment) => [
                'path' => $attachment->absolutePath(),
                'name' => $attachment->original_name,
                'mime' => $attachment->mime_type,
            ])->all(),
            $message->use_template
        );
    }

    private function emailList(?string $value): array
    {
        return collect(explode(',', (string) $value))
            ->map(fn ($email) => trim($email))
            ->filter()
            ->values()
            ->all();
    }

    private function validatedAccount(Request $request, ?EmailAccount $account = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'smtp_host' => ['required', 'string', 'max:255'],
            'smtp_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'smtp_username' => ['required', 'string', 'max:255'],
            'smtp_password' => [$account ? 'nullable' : 'required', 'string', 'max:1000'],
            'smtp_encryption' => ['nullable', Rule::in(['tls', 'ssl'])],
            'imap_host' => ['nullable', 'string', 'max:255'],
            'imap_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'imap_username' => ['nullable', 'string', 'max:255'],
            'imap_password' => ['nullable', 'string', 'max:1000'],
            'imap_encryption' => ['nullable', Rule::in(['tls', 'ssl'])],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
