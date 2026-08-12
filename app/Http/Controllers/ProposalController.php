<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProposalRequest;
use App\Models\Conversation;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use App\Services\CoverLetterGeneratorService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProposalController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->role === 'creator', 403);

        $proposals = Proposal::where('creator_profile_id', $request->user()->creatorProfile?->id)
            ->with('project.categories')
            ->latest()
            ->paginate(15);

        return view('proposals.index', ['proposals' => $proposals]);
    }

    public function store(StoreProposalRequest $request, Project $project): RedirectResponse
    {
        if ($project->status !== 'open') {
            return back()->withErrors(['project' => __('Овој проект повеќе не прима понуди.')]);
        }

        $creatorProfile = $request->user()->creatorProfile;

        $alreadyApplied = Proposal::where('project_id', $project->id)
            ->where('creator_profile_id', $creatorProfile->id)
            ->exists();

        if ($alreadyApplied) {
            return back()->withErrors(['project' => __('Веќе имаш поднесено понуда за овој проект.')]);
        }

        $validated = $request->validated();

        try {
            Proposal::create([
                'project_id' => $project->id,
                'creator_profile_id' => $creatorProfile->id,
                'message' => $validated['message'],
                'price' => $validated['price'],
                'status' => 'pending',
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->withErrors(['project' => __('Веќе имаш поднесено понуда за овој проект.')]);
            }

            throw $e;
        }

        $this->startProposalConversation($project, $request->user(), $validated['message'], $validated['price']);

        return redirect()->route('projects.show', $project)->with('status', __('Понудата е испратена.'));
    }

    public function generateCoverLetter(Request $request, Project $project, CoverLetterGeneratorService $service): JsonResponse
    {
        $this->authorize('submitProposal', $project);

        if ($project->status !== 'open') {
            return response()->json(['message' => __('Овој проект повеќе не прима понуди.')], 422);
        }

        $creatorProfile = $request->user()->creatorProfile;

        $alreadyApplied = Proposal::where('project_id', $project->id)
            ->where('creator_profile_id', $creatorProfile->id)
            ->exists();

        if ($alreadyApplied) {
            return response()->json(['message' => __('Веќе имаш поднесено понуда за овој проект.')], 422);
        }

        $project->loadMissing(['categories', 'skills']);
        $creatorProfile->loadMissing(['categories', 'skills']);

        $message = $service->generate($project, $creatorProfile);

        if (! $message) {
            return response()->json([
                'message' => __('AI генерирањето моментално не е достапно. Обиди се повторно подоцна или напиши ја пораката сам.'),
            ], 503);
        }

        return response()->json(['message' => $message]);
    }

    private function startProposalConversation(Project $project, User $creator, string $message, float $price): void
    {
        $conversation = Conversation::findOrCreateBetween($creator, $project->client, $project->id);

        $conversation->messages()->create([
            'sender_id' => $creator->id,
            'body' => $message."\n\n".__('Понудена цена').': '.$price.' EUR',
        ]);

        $conversation->touch();
    }

    public function accept(Request $request, Proposal $proposal): RedirectResponse
    {
        $project = $proposal->project;

        $this->authorize('manageProposals', $project);

        $proposal->accept();

        return redirect()->route('projects.show', $project)->with('status', __('Понудата е прифатена и договорот е креиран.'));
    }

    public function reject(Request $request, Proposal $proposal): RedirectResponse
    {
        $project = $proposal->project;

        $this->authorize('manageProposals', $project);

        $proposal->reject();

        return redirect()->route('projects.show', $project)->with('status', __('Понудата е одбиена.'));
    }
}
