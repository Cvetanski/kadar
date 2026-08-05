<?php

namespace App\Services;

use App\Mail\NewProjectNotification;
use App\Models\CreatorProfile;
use App\Models\Project;
use Illuminate\Support\Facades\Mail;

class NewProjectNotifier
{
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

        CreatorProfile::where('verified', true)
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
            ->whereHas('user', fn ($q) => $q->where('email_notifications_enabled', true))
            ->with('user')
            ->chunk(50, function ($creatorProfiles) use ($project) {
                foreach ($creatorProfiles as $creatorProfile) {
                    Mail::to($creatorProfile->user->email)
                        ->locale($creatorProfile->user->locale ?? 'mk')
                        ->queue(new NewProjectNotification($project));
                }
            });
    }
}
