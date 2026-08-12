<?php

namespace App\Services;

use App\Mail\ProjectInvitationReceived;
use App\Models\ProjectInvitation;
use Illuminate\Support\Facades\Mail;

class ProjectInvitationNotifier
{
    public function notify(ProjectInvitation $invitation): void
    {
        $recipient = $invitation->creatorProfile->user;

        if (! $recipient->email_notifications_enabled) {
            return;
        }

        Mail::to($recipient->email)
            ->locale($recipient->locale ?? 'mk')
            ->send(new ProjectInvitationReceived($recipient, $invitation));
    }
}
