<?php

namespace App\Console\Commands;

use App\Models\CreatorProfile;
use Illuminate\Console\Command;

class BackfillCreatorProfileSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'creators:backfill-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a slug for any creator profile that does not have one yet';

    public function handle(): int
    {
        $profiles = CreatorProfile::whereNull('slug')->with('user')->get();

        if ($profiles->isEmpty()) {
            $this->info('No creator profiles are missing a slug.');

            return self::SUCCESS;
        }

        foreach ($profiles as $profile) {
            $slug = CreatorProfile::generateUniqueSlug($profile->user?->name ?? 'creator', $profile->id);
            $profile->update(['slug' => $slug]);
            $this->line("#{$profile->id} -> {$slug}");
        }

        $this->info("Backfilled {$profiles->count()} creator profile slug(s).");

        return self::SUCCESS;
    }
}
