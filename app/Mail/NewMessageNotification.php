<?php

namespace App\Mail;

use App\Models\Message;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewMessageNotification extends Mailable
{
    public function __construct(public Message $chatMessage) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Нова порака од :name', ['name' => $this->chatMessage->sender->name]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-message-notification',
            with: ['chatMessage' => $this->chatMessage],
        );
    }
}
