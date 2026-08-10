<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewProjectNotification extends Mailable
{
    public function __construct(public Project $project) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Нов оглас: :title', ['title' => $this->project->title]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-project-notification',
            with: ['project' => $this->project],
        );
    }
}
