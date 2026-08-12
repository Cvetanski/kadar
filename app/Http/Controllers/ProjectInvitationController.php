<?php

namespace App\Http\Controllers;

use App\Models\CreatorProfile;
use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Services\ProjectInvitationNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectInvitationController extends Controller
{
    public function store(Request $request, CreatorProfile $creatorProfile, ProjectInvitationNotifier $notifier): RedirectResponse
    {
        $client = $request->user();

        abort_unless($client->role === 'client', 403);

        $validated = $request->validate([
            'project_id' => ['required', 'integer'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $project = Project::where('id', $validated['project_id'])
            ->where('client_id', $client->id)
            ->where('status', 'open')
            ->first();

        if (! $project) {
            return back()->with('error', __('Овој проект не е достапен за покана.'));
        }

        // Checked up front rather than relying solely on the DB unique
        // constraint, so the client gets a clear message instead of a raw
        // query-exception page if they somehow resubmit.
        $alreadyInvited = ProjectInvitation::where('project_id', $project->id)
            ->where('creator_profile_id', $creatorProfile->id)
            ->exists();

        if ($alreadyInvited) {
            return back()->with('error', __('Веќе си испратил покана до овој креативец за овој проект.'));
        }

        $invitation = ProjectInvitation::create([
            'project_id' => $project->id,
            'creator_profile_id' => $creatorProfile->id,
            'client_id' => $client->id,
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ]);

        $conversation = $invitation->send();
        $notifier->notify($invitation);

        return redirect()->route('messages.show', $conversation)->with('status', __('Поканата е испратена.'));
    }
}
