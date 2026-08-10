<?php

namespace App\Services;

use App\Mail\OnboardingReminder;
use App\Models\CreatorProfile;
use Illuminate\Support\Facades\Mail;

class OnboardingReminderNotifier
{
    /**
     * Email every creator who hasn't completed onboarding yet, skipping
     * anyone who has disabled email notifications. Sent synchronously (not
     * queued) so the admin sees them go out immediately. Returns how many
     * reminders were sent.
     */
    public function notifyAll(): int
    {
        $sent = 0;

        CreatorProfile::whereNull('onboarding_completed_at')
            ->whereHas('user', fn ($q) => $q->where('email_notifications_enabled', true))
            ->with('user')
            ->chunk(50, function ($creatorProfiles) use (&$sent) {
                foreach ($creatorProfiles as $creatorProfile) {
                    Mail::to($creatorProfile->user->email)
                        ->locale($creatorProfile->user->locale ?? 'mk')
                        ->send(new OnboardingReminder($creatorProfile->user));

                    $sent++;
                }
            });

        return $sent;
    }
}
