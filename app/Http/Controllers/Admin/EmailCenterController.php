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
            $parsed = $this->parsedImapMessage($imap, (int) $uid);
            $bodyText = $parsed['text'] ?: '-';

            return (object) [
                'uid' => $uid,
                'from' => $overview?->from ?? '-',
                'subject' => imap_utf8($overview?->subject ?? '(tanpa subject)'),
                'date' => isset($overview->date) ? date('d/m/Y H:i', strtotime($overview->date)) : '-',
                'body' => $bodyText,
                'body_html' => $parsed['html'],
                'preview' => str($bodyText)->squish()->limit(160)->toString(),
                'seen' => (bool) ($overview?->seen ?? false),
                'has_attachment' => count($parsed['attachments']) > 0,
                'attachments' => $parsed['attachments'],
            ];
        });
        imap_close($imap);

        return [$messages, null];
    }

    private function parsedImapMessage($imap, int $uid): array
    {
        $structure = imap_fetchstructure($imap, (string) $uid, FT_UID);
        $parts = [
            'html' => null,
            'plain' => null,
            'attachments' => [],
        ];

        if ($structure) {
            $this->walkImapPart($imap, $uid, $structure, '', $parts);
        }

        $plain = $this->cleanEmailText($parts['plain'] ?? '');
        $html = $this->cleanEmailHtml($parts['html'] ?? '');

        if ($html === '' && $plain !== '') {
            $html = nl2br(e($plain));
        }

        $text = $html !== ''
            ? $this->cleanEmailText(strip_tags($html))
            : $plain;

        return [
            'html' => $html,
            'text' => $text,
            'attachments' => array_values(array_unique(array_filter($parts['attachments']))),
        ];
    }

    private function walkImapPart($imap, int $uid, object $part, string $partNumber, array &$result): void
    {
        $currentPart = $partNumber !== '' ? $partNumber : '1';
        $isAttachment = false;
        $attachmentName = null;

        foreach (['dparameters', 'parameters'] as $parameterGroup) {
            foreach ($part->{$parameterGroup} ?? [] as $parameter) {
                $attribute = strtolower($parameter->attribute ?? '');
                if (in_array($attribute, ['filename', 'name'], true)) {
                    $isAttachment = true;
                    $attachmentName = $this->decodeMimeText((string) ($parameter->value ?? ''));
                }
            }
        }

        if (isset($part->disposition) && strtolower($part->disposition) === 'attachment') {
            $isAttachment = true;
        }

        if ($isAttachment) {
            $result['attachments'][] = $attachmentName ?: 'Attachment';
        }

        if (! empty($part->parts)) {
            foreach ($part->parts as $index => $subPart) {
                $nextPartNumber = $partNumber === '' ? (string) ($index + 1) : $partNumber . '.' . ($index + 1);
                $this->walkImapPart($imap, $uid, $subPart, $nextPartNumber, $result);
            }

            return;
        }

        if ($isAttachment || (int) ($part->type ?? -1) !== TYPETEXT) {
            return;
        }

        $subtype = strtolower($part->subtype ?? 'plain');
        $body = imap_fetchbody($imap, (string) $uid, $currentPart, FT_UID | FT_PEEK);
        $body = $this->decodeImapBody((string) $body, (int) ($part->encoding ?? 0));

        if ($subtype === 'html' && ! filled($result['html'])) {
            $result['html'] = $body;
        }

        if ($subtype === 'plain' && ! filled($result['plain'])) {
            $result['plain'] = $body;
        }
    }

    private function decodeImapBody(string $body, int $encoding): string
    {
        return match ($encoding) {
            ENCBASE64 => base64_decode($body, true) ?: '',
            ENCQUOTEDPRINTABLE => quoted_printable_decode($body),
            default => $body,
        };
    }

    private function cleanEmailHtml(string $html): string
    {
        if ($html === '') {
            return '';
        }

        $html = preg_replace('/<\s*(script|style|meta|link|title)[^>]*>.*?<\s*\/\s*\1\s*>/is', '', $html) ?? $html;
        $html = preg_replace('/<\s*(script|style|meta|link|title|html|head|body)[^>]*\/?>/is', '', $html) ?? $html;
        $html = preg_replace('/<\/\s*(html|head|body)\s*>/is', '', $html) ?? $html;
        $html = preg_replace('/\s(on[a-z]+|style|class|id)\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/is', '', $html) ?? $html;
        $html = preg_replace('/(href|src)\s*=\s*("|\')\s*javascript:.*?\2/is', '$1="#"', $html) ?? $html;
        $html = strip_tags($html, '<p><br><div><span><strong><b><em><i><u><a><ul><ol><li><table><thead><tbody><tr><td><th><blockquote>');

        return trim($html);
    }

    private function cleanEmailText(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/--[a-z0-9=_+\-.]{12,}.*/i', '', $text) ?? $text;
        $text = preg_replace('/^(content-type|content-transfer-encoding|content-disposition|mime-version|boundary):.*$/mi', '', $text) ?? $text;
        $text = preg_replace('/\b[A-Za-z0-9+\/]{120,}={0,2}\b/', '', $text) ?? $text;
        $text = preg_replace("/[ \t]+\n/", "\n", $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        return trim($text);
    }

    private function decodeMimeText(string $value): string
    {
        return imap_utf8($value);
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
        $body = $this->sanitizeOutgoingEmailHtml($message->body ?: '');
        if (! $message->use_template) {
            $body = '<div style="font-family:Arial,sans-serif;line-height:1.7;">' . $body . '</div>';
        }

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

    private function sanitizeOutgoingEmailHtml(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }

        $html = str_replace(["\r\n", "\r"], "\n", $html);
        $hasHtmlTags = preg_match('/<\s*\/?\s*(p|br|strong|b|em|i|u|ul|ol|li|a|blockquote|h[1-6]|table|thead|tbody|tr|td|th|span|div)\b/i', $html) === 1;

        if (! $hasHtmlTags) {
            return nl2br(e($html));
        }

        $html = preg_replace('/<\s*(script|style|iframe|object|embed|form|input|button|meta|link|title)[^>]*>.*?<\s*\/\s*\1\s*>/is', '', $html) ?? $html;
        $html = preg_replace('/<\s*(script|style|iframe|object|embed|form|input|button|meta|link|title)[^>]*\/?>/is', '', $html) ?? $html;
        $html = preg_replace('/\s(on[a-z]+|style|class|id)\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/is', '', $html) ?? $html;
        $html = preg_replace('/(href|src)\s*=\s*("|\')\s*javascript:.*?\2/is', '$1="#"', $html) ?? $html;

        return trim(strip_tags($html, '<p><br><strong><b><em><i><u><ul><ol><li><a><blockquote><h1><h2><h3><h4><h5><h6><table><thead><tbody><tr><td><th><span><div>'));
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
