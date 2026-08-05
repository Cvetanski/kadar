<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\CreatorProfile;
use App\Models\Project;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->role === 'client') {
            return $this->clientDashboard($request);
        }

        if ($user->role !== 'creator') {
            return view('dashboard');
        }

        $profile = $user->creatorProfile;

        $stats = [
            'pending_proposals' => Proposal::where('creator_profile_id', $profile?->id)->where('status', 'pending')->count(),
            'active_contracts' => Contract::where('creator_profile_id', $profile?->id)->where('status', 'active')->count(),
            'completed_contracts' => Contract::where('creator_profile_id', $profile?->id)->where('status', 'completed')->count(),
        ];

        $categoryIds = $profile?->categories->pluck('id')->all() ?? [];

        $recommendedProjects = Project::where('status', 'open')
            ->when($categoryIds !== [], fn ($q) => $q->whereHas('categories', fn ($q2) => $q2->whereIn('categories.id', $categoryIds)))
            ->with(['categories', 'country', 'city', 'client' => function ($query) {
                $query->withCount('reviewsReceived')->withAvg('reviewsReceived', 'rating');
            }])
            ->latest()
            ->limit(6)
            ->get();

        return view('dashboard-creator', [
            'profile' => $profile,
            'stats' => $stats,
            'recommendedProjects' => $recommendedProjects,
        ]);
    }

    private function clientDashboard(Request $request): View
    {
        $user = $request->user();

        $stats = [
            'open_projects' => Project::where('client_id', $user->id)->where('status', 'open')->count(),
            'active_contracts' => Contract::where('client_id', $user->id)->where('status', 'active')->count(),
            'completed_contracts' => Contract::where('client_id', $user->id)->where('status', 'completed')->count(),
            'favorites' => $user->favorites()->count(),
        ];

        $categoryIds = Project::where('client_id', $user->id)
            ->with('categories:id')
            ->get()
            ->pluck('categories')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->values()
            ->all();

        $recommendedCreators = CreatorProfile::whereNotNull('onboarding_completed_at')
            ->where('verified', true)
            ->when($categoryIds !== [], fn ($q) => $q->whereHas('categories', fn ($q2) => $q2->whereIn('categories.id', $categoryIds)))
            ->with(['user.country', 'user.city', 'categories'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('dashboard-client', [
            'stats' => $stats,
            'recommendedCreators' => $recommendedCreators,
        ]);
    }
}
