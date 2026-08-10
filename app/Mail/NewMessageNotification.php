<?php

namespace App\Mail;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewMessageNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

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
