<?php

namespace App\Services;

use App\Mail\CreatorVerified;
use App\Models\CreatorProfile;
use Illuminate\Support\Facades\Mail;

class CreatorVerifiedNotifier
{
    public function notify(CreatorProfile $creatorProfile): void
    {
        $recipient = $creatorProfile->user;

        if (! $recipient->email_notifications_enabled) {
            return;
        }

        Mail::to($recipient->email)
            ->locale($recipient->locale ?? 'mk')
            ->send(new CreatorVerified($recipient));
    }
}
