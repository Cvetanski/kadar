<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\CreatorProfile;
use App\Models\Project;
use App\Models\User;
use App\Services\NewMessageNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function startWithCreator(Request $request, CreatorProfile $creatorProfile): RedirectResponse
    {
        $user = $request->user();
        $recipient = $creatorProfile->user;

        abort_if($user->id === $recipient->id, 403);

        $conversation = Conversation::findOrCreateBetween($user, $recipient);

        return redirect()->route('messages.show', $conversation);
    }

    public function startWithClient(Request $request, User $client): RedirectResponse
    {
        $user = $request->user();

        abort_if($user->id === $client->id, 403);
        abort_unless($client->role === 'client', 404);

        $projectId = null;
        if ($request->filled('project_id')) {
            $projectId = Project::where('id', $request->integer('project_id'))
                ->where('client_id', $client->id)
                ->value('id');
        }

        $conversation = Conversation::findOrCreateBetween($user, $client, $projectId);

        return redirect()->route('messages.show', $conversation);
    }

    public function index(Request $request): View
    {
        return view('messages.index', ['conversation' => null]);
    }

    public function show(Request $request, Conversation $conversation): View
    {
        abort_unless($conversation->participants->contains('id', $request->user()->id), 403);

        return view('messages.index', ['conversation' => $conversation]);
    }

    public function store(Request $request, Conversation $conversation, NewMessageNotifier $notifier): RedirectResponse
    {
        $user = $request->user();

        abort_unless($conversation->participants->contains('id', $user->id), 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'body' => $validated['body'],
        ]);

        $conversation->touch();

        $notifier->notify($message);

        return redirect()->route('messages.show', $conversation);
    }
}
