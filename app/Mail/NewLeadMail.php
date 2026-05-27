<?php

namespace App\Mail;

use App\Models\Lead;
use App\Models\WebsiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewLeadMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
        public ?WebsiteSetting $websiteSetting = null
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Lead Baru dari Website');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.lead-notification');
    }

    public function attachments(): array
    {
        return [];
    }
}
