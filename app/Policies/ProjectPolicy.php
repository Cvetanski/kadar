<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function create(User $user): bool
    {
        return $user->role === 'client';
    }

    public function manageProposals(User $user, Project $project): bool
    {
        return $user->id === $project->client_id;
    }

    public function update(User $user, Project $project): bool
    {
        return $user->id === $project->client_id && $project->status === 'open';
    }

    public function cancel(User $user, Project $project): bool
    {
        return $user->id === $project->client_id && $project->status === 'open';
    }

    public function submitProposal(User $user, Project $project): bool
    {
        return $user->role === 'creator'
            && $user->creatorProfile
            && $user->creatorProfile->onboarding_completed_at !== null;
    }
}
