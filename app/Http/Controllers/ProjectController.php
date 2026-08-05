<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Category;
use App\Models\Country;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Review;
use App\Models\Skill;
use App\Services\ProjectDescriptionImproverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->role === 'client', 403);

        $projects = Project::where('client_id', $request->user()->id)
            ->with('categories')
            ->withCount('proposals')
            ->latest()
            ->paginate(15);

        return view('projects.index', ['projects' => $projects]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Project::class);

        return view('projects.create', [
            'categories' => Category::orderBy('slug')->get(),
            'countries' => Country::with('cities')->get(),
            'skillsByCategory' => Category::orderBy('id')->with('skills')->get()
                ->mapWithKeys(fn ($category) => [$category->id => $category->skills])
                ->filter(fn ($skills) => $skills->isNotEmpty()),
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $budgetNegotiable = $request->boolean('budget_negotiable');

        $project = Project::create([
            'client_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'budget_min' => $budgetNegotiable ? null : ($validated['budget_min'] ?? null),
            'budget_max' => $budgetNegotiable ? null : ($validated['budget_max'] ?? null),
            'deadline' => $validated['deadline'] ?? null,
            'country_id' => $validated['country_id'] ?? null,
            'city_id' => $validated['city_id'] ?? null,
            'remote_ok' => $request->boolean('remote_ok'),
            'status' => 'open',
        ]);

        $project->categories()->sync($validated['category_ids']);
        $project->skills()->sync($this->skillIdsForCategories($validated['skill_ids'] ?? [], $validated['category_ids']));

        return redirect()->route('projects.show', $project)->with('status', __('Огласот е објавен.'));
    }

    public function improveDescription(Request $request, ProjectDescriptionImproverService $service): JsonResponse
    {
        $this->authorize('create', Project::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ]);

        $categoryNames = Category::whereIn('id', $validated['category_ids'] ?? [])->pluck('name')->all();

        $result = $service->improve($validated['title'], $validated['description'], $categoryNames);

        if (! $result) {
            return response()->json([
                'message' => __('AI подобрувањето моментално не е достапно. Обиди се повторно подоцна.'),
            ], 503);
        }

        return response()->json($result);
    }

    public function edit(Request $request, Project $project): View
    {
        $this->authorize('update', $project);

        return view('projects.edit', [
            'project' => $project,
            'categories' => Category::orderBy('slug')->get(),
            'countries' => Country::with('cities')->get(),
            'skillsByCategory' => Category::orderBy('id')->with('skills')->get()
                ->mapWithKeys(fn ($category) => [$category->id => $category->skills])
                ->filter(fn ($skills) => $skills->isNotEmpty()),
            'selectedCategoryIds' => $project->categories->pluck('id')->all(),
            'selectedSkillIds' => $project->skills->pluck('id')->all(),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $validated = $request->validated();

        $budgetNegotiable = $request->boolean('budget_negotiable');

        $project->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'budget_min' => $budgetNegotiable ? null : ($validated['budget_min'] ?? null),
            'budget_max' => $budgetNegotiable ? null : ($validated['budget_max'] ?? null),
            'deadline' => $validated['deadline'] ?? null,
            'country_id' => $validated['country_id'] ?? null,
            'city_id' => $validated['city_id'] ?? null,
            'remote_ok' => $request->boolean('remote_ok'),
        ]);

        $project->categories()->sync($validated['category_ids']);
        $project->skills()->sync($this->skillIdsForCategories($validated['skill_ids'] ?? [], $validated['category_ids']));

        return redirect()->route('projects.show', $project)->with('status', __('Проектот е ажуриран.'));
    }

    /**
     * Only keep skill ids that actually belong to one of the selected
     * categories, so a tampered request can't attach unrelated skills.
     *
     * @param  array<int>  $skillIds
     * @param  array<int>  $categoryIds
     * @return array<int>
     */
    private function skillIdsForCategories(array $skillIds, array $categoryIds): array
    {
        if ($skillIds === []) {
            return [];
        }

        return Skill::whereIn('id', $skillIds)
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
            ->pluck('id')
            ->all();
    }

    public function cancel(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('cancel', $project);

        $project->update(['status' => 'cancelled']);

        return redirect()->route('projects.show', $project)->with('status', __('Огласот е затворен.'));
    }

    public function show(Request $request, Project $project): View
    {
        $project->load(['categories', 'country', 'city', 'client']);

        $user = $request->user();
        $isOwner = $user->id === $project->client_id;

        $proposals = null;
        $existingProposal = null;
        $canApply = false;

        if ($isOwner) {
            $proposals = $project->proposals()
                ->with(['creatorProfile.user'])
                ->latest()
                ->get();
        } elseif ($user->role === 'creator' && $user->creatorProfile) {
            $existingProposal = Proposal::where('project_id', $project->id)
                ->where('creator_profile_id', $user->creatorProfile->id)
                ->first();

            $canApply = $project->status === 'open'
                && ! $existingProposal
                && $user->creatorProfile->onboarding_completed_at !== null;
        }

        $contract = $project->contracts()->whereIn('status', ['active', 'completed'])->latest()->first();

        $isContractParty = false;
        $myReview = null;

        if ($contract) {
            $isContractParty = $isOwner
                || ($user->creatorProfile && $user->creatorProfile->id === $contract->creator_profile_id);

            if ($isContractParty) {
                $contract->load('creatorProfile.user');

                if ($contract->status === 'completed') {
                    $myReview = Review::where('contract_id', $contract->id)
                        ->where('reviewer_id', $user->id)
                        ->first();
                }
            }
        }

        return view('projects.show', [
            'project' => $project,
            'isOwner' => $isOwner,
            'proposals' => $proposals,
            'existingProposal' => $existingProposal,
            'canApply' => $canApply,
            'contract' => $isContractParty ? $contract : null,
            'myReview' => $myReview,
        ]);
    }
}
