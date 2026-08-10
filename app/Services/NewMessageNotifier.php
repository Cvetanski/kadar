<?php

namespace App\Services;

use App\Mail\NewMessageNotification;
use App\Models\Message;
use Illuminate\Support\Facades\Mail;

class NewMessageNotifier
{
    /**
     * Email every conversation participant except the sender, unless
     * they've turned off notifications.
     */
    public function notify(Message $message): void
    {
        $message->conversation->participants()
            ->where('users.id', '!=', $message->sender_id)
            ->where('users.email_notifications_enabled', true)
            ->get()
            ->each(function ($recipient) use ($message) {
                Mail::to($recipient->email)
                    ->locale($recipient->locale ?? 'mk')
                    ->send(new NewMessageNotification($message));
            });
    }
}
