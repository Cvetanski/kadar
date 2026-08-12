<?php

namespace App\Mail;

use App\Models\ProjectInvitation;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ProjectInvitationReceived extends Mailable
{
    public function __construct(public User $recipient, public ProjectInvitation $invitation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __(':client те покани на проект', ['client' => $this->invitation->client->name]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.project-invitation-received',
            text: 'emails.project-invitation-received-text',
            with: [
                'recipient' => $this->recipient,
                'invitation' => $this->invitation,
            ],
        );
    }
}
