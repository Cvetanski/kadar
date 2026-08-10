<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class OnboardingReminder extends Mailable
{
    public function __construct(public User $recipient) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Уште еден чекор до верификуван профил на CreatorSpot'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.onboarding-reminder',
            with: ['recipient' => $this->recipient],
        );
    }
}
