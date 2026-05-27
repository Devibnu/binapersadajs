<?php

namespace App\Mail;

use App\Models\EmailSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SmtpTestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public EmailSetting $emailSetting)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Test SMTP Bina Persada JS');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.smtp-test');
    }

    public function attachments(): array
    {
        return [];
    }
}
