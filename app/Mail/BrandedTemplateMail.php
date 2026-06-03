<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\HtmlString;

class BrandedTemplateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $mailSubject,
        public string $bodyHtml,
        public ?EmailTemplate $emailTemplate = null,
        public array $variables = []
    ) {
        $this->emailTemplate ??= EmailTemplate::current();
    }

    public function envelope(): Envelope
    {
        $from = filled($this->emailTemplate?->sender_email)
            ? new Address($this->emailTemplate->sender_email, $this->emailTemplate->sender_name ?: $this->emailTemplate->company_name)
            : null;

        if ($from) {
            return new Envelope(from: $from, subject: $this->mailSubject);
        }

        return new Envelope(subject: $this->mailSubject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.branded-template',
            with: [
                'renderedHeaderHtml' => $this->headerHtml(),
                'renderedBodyHtml' => $this->body(),
                'renderedFooterHtml' => $this->footerHtml(),
                'renderedDisclaimerHtml' => $this->disclaimerHtml(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function headerHtml(): HtmlString
    {
        return new HtmlString($this->renderTokens($this->emailTemplate?->header_html ?: ''));
    }

    public function footerHtml(): HtmlString
    {
        return new HtmlString($this->renderTokens($this->emailTemplate?->footer_html ?: ''));
    }

    public function disclaimerHtml(): HtmlString
    {
        return new HtmlString($this->renderTokens($this->emailTemplate?->disclaimer_html ?: ''));
    }

    public function body(): HtmlString
    {
        return new HtmlString($this->renderTokens($this->bodyHtml));
    }

    private function renderTokens(string $html): string
    {
        $template = $this->emailTemplate;
        $logoUrl = $template?->logoUrl();
        $logoHtml = $logoUrl
            ? '<img src="' . e($logoUrl) . '" alt="' . e($template->company_name) . '" style="display:block;max-height:64px;max-width:220px;">'
            : '<div style="font-size:20px;font-weight:bold;">' . e($template?->company_name ?: 'PT. Bina Persada Jaya Sejahtera') . '</div>';

        $tokens = array_merge([
            'logo' => $logoHtml,
            'company_name' => e($template?->company_name ?: 'PT. Bina Persada Jaya Sejahtera'),
            'address' => nl2br(e($template?->address ?: '-')),
            'phone' => e($template?->phone ?: '-'),
            'whatsapp' => e($template?->whatsapp ?: '-'),
            'email' => e($template?->sender_email ?: config('mail.from.address')),
            'website' => e($template?->website ?: config('app.url')),
        ], $this->variables);

        foreach ($tokens as $key => $value) {
            $html = str_replace('{{' . $key . '}}', (string) $value, $html);
        }

        return $html;
    }
}
