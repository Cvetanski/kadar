<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MeetingRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $requesterName,
        public string $requesterEmail,
        public string $meetingDate,
        public string $meetingTime,
        public ?string $note,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ново барање за состанок — '.$this->requesterName,
            replyTo: [$this->requesterEmail],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.meeting-request',
        );
    }
}
