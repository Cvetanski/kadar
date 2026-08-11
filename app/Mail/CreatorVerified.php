<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CreatorVerified extends Mailable
{
    public function __construct(public User $recipient) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Твојот профил на CreatorSpot е верификуван 🎉'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.creator-verified',
            text: 'emails.creator-verified-text',
            with: ['recipient' => $this->recipient],
        );
    }
}
