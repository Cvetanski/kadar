<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CreatorProfile;
use App\Models\Review;
use App\Services\AvatarUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CreatorProfileController extends Controller
{
    public function index(): View
    {
        return view('creators.index', [
            'categories' => Category::orderBy('slug')->get(),
        ]);
    }

    public function show(Request $request, CreatorProfile $creatorProfile): View
    {
        $creatorProfile->load(['user', 'categories', 'skills', 'portfolioItems']);

        $user = $request->user();
        $isOwnProfile = $user->id === $creatorProfile->user_id;
        $isFavorited = ! $isOwnProfile && $user->favorites()->where('creator_profile_id', $creatorProfile->id)->exists();

        $reviews = Review::where('reviewee_id', $creatorProfile->user_id)
            ->with('reviewer')
            ->latest()
            ->get();

        return view('creators.show', [
            'creatorProfile' => $creatorProfile,
            'isOwnProfile' => $isOwnProfile,
            'isFavorited' => $isFavorited,
            'reviews' => $reviews,
            'averageRating' => $reviews->isNotEmpty() ? $reviews->avg('rating') : 0,
        ]);
    }

    public function edit(Request $request, CreatorProfile $creatorProfile): View
    {
        abort_unless($request->user()->id === $creatorProfile->user_id, 403);

        $creatorProfile->load('categories', 'skills');

        return view('creators.edit', [
            'creatorProfile' => $creatorProfile,
            'categories' => Category::orderBy('id')->get(),
            'skillsByCategory' => Category::orderBy('id')->with('skills')->get()
                ->mapWithKeys(fn ($category) => [$category->id => $category->skills])
                ->filter(fn ($skills) => $skills->isNotEmpty()),
            'selectedCategoryIds' => $creatorProfile->categories->pluck('id')->all(),
            'selectedSkillIds' => $creatorProfile->skills->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, CreatorProfile $creatorProfile, AvatarUploadService $avatarUploadService): RedirectResponse
    {
        abort_unless($request->user()->id === $creatorProfile->user_id, 403);

        $validated = $request->validate([
            'avatar' => ['nullable', 'image', 'max:2048'],
            'headline' => ['required', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'project_rate_from' => ['nullable', 'numeric', 'min:0', 'max:99999'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
            'remote_ok' => ['boolean'],
            'category_ids' => ['array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'skill_ids' => ['array'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
        ]);

        $creatorProfile->update([
            'headline' => $validated['headline'],
            'bio' => $validated['bio'] ?? null,
            'hourly_rate' => $validated['hourly_rate'] ?? null,
            'project_rate_from' => $validated['project_rate_from'] ?? null,
            'experience_years' => $validated['experience_years'] ?? 0,
            'remote_ok' => $request->boolean('remote_ok'),
            'instagram_url' => $validated['instagram_url'] ?? null,
            'facebook_url' => $validated['facebook_url'] ?? null,
            'website_url' => $validated['website_url'] ?? null,
        ]);

        $creatorProfile->categories()->sync($validated['category_ids'] ?? []);
        $creatorProfile->skills()->sync($validated['skill_ids'] ?? []);

        if ($request->hasFile('avatar')) {
            $request->user()->update([
                'avatar_url' => $avatarUploadService->store($request->file('avatar')),
            ]);
        }

        return redirect()->route('creators.show', $creatorProfile)->with('status', __('Профилот е ажуриран.'));
    }
}
