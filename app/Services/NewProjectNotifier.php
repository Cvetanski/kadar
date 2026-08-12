<?php

namespace App\Services;

use App\Mail\NewProjectNotification;
use App\Models\CreatorProfile;
use App\Models\Project;
use Illuminate\Support\Facades\Mail;

class NewProjectNotifier
{
    /**
     * Resend's rate limit is 10 requests/second, so emails are sent in
     * batches with a pause between them to stay safely under that limit.
     */
    private const BATCH_SIZE = 9;

    private const BATCH_PAUSE_SECONDS = 3;

    /**
     * Email every verified creator whose selected categories overlap with
     * the project's categories, unless they've turned off notifications.
     */
    public function notify(Project $project): void
    {
        $categoryIds = $project->categories->pluck('id');

        if ($categoryIds->isEmpty()) {
            return;
        }

        $sent = 0;

        CreatorProfile::where('verified', true)
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
            ->whereHas('user', fn ($q) => $q->where('email_notifications_enabled', true))
            ->with('user')
            ->chunk(50, function ($creatorProfiles) use ($project, &$sent) {
                foreach ($creatorProfiles as $creatorProfile) {
                    if ($sent > 0 && $sent % self::BATCH_SIZE === 0) {
                        sleep(self::BATCH_PAUSE_SECONDS);
                    }

                    Mail::to($creatorProfile->user->email)
                        ->locale($creatorProfile->user->locale ?? 'mk')
                        ->send(new NewProjectNotification($project));

                    $sent++;
                }
            });
    }
}
