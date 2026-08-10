<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WelcomeEmail extends Mailable
{
    public function __construct(public User $recipient) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Добредојде на CreatorSpot!'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            text: 'emails.welcome-text',
            with: ['recipient' => $this->recipient],
        );
    }
}
