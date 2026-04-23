<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly object $user,
        public readonly string $otp,
        public readonly int    $ttlMinutes = 5
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'رمز التحقق الخاص بك — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'name'       => $this->user->name,
                'code'       => $this->otp,
                'ttl'        => $this->ttlMinutes,
                'appName'    => config('app.name'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}