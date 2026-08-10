<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Country;
use App\Models\CreatorProfile;
use App\Models\User;
use App\Services\AvatarUploadService;
use App\Services\OnboardingReminderNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function verifications(): View
    {
        $pending = CreatorProfile::whereNotNull('onboarding_completed_at')
            ->where('verified', false)
            ->with('user')
            ->orderBy('onboarding_completed_at')
            ->get();

        return view('admin.verifications', ['pending' => $pending]);
    }

    public function verify(CreatorProfile $creatorProfile): RedirectResponse
    {
        abort_unless($creatorProfile->onboarding_completed_at !== null, 403);

        $creatorProfile->update(['verified' => true]);

        return back()->with('status', __('Креативецот е верификуван.'));
    }

    public function users(Request $request): View
    {
        $role = $request->input('role', 'creator');
        $role = in_array($role, ['creator', 'client', 'pending_verification'], true) ? $role : 'creator';

        $users = null;
        $pending = null;

        if ($role === 'pending_verification') {
            $pending = CreatorProfile::whereNotNull('onboarding_completed_at')
                ->where('verified', false)
                ->with('user')
                ->orderBy('onboarding_completed_at')
                ->get();
        } else {
            $users = User::where('role', $role)
                ->when($role === 'creator', fn ($q) => $q->with('creatorProfile'))
                ->when($role === 'client', fn ($q) => $q->withCount('projects'))
                ->latest()
                ->paginate(20)
                ->withQueryString();
        }

        return view('admin.users', [
            'users' => $users,
            'pending' => $pending,
            'role' => $role,
            'creatorCount' => User::where('role', 'creator')->count(),
            'clientCount' => User::where('role', 'client')->count(),
            'pendingVerificationCount' => CreatorProfile::whereNotNull('onboarding_completed_at')
                ->where('verified', false)
                ->count(),
            'incompleteOnboardingCount' => CreatorProfile::whereNull('onboarding_completed_at')->count(),
        ]);
    }

    public function remindIncompleteOnboarding(OnboardingReminderNotifier $notifier): RedirectResponse
    {
        $count = $notifier->notifyAll();

        return back()->with('status', __(':count потсетници се испратени.', ['count' => $count]));
    }

    public function convertClientToCreator(User $user): RedirectResponse
    {
        abort_unless($user->role === 'client', 404);

        $user->update(['role' => 'creator']);
        CreatorProfile::firstOrCreate(['user_id' => $user->id]);

        return redirect()->route('admin.users', ['role' => 'creator'])
            ->with('status', __(':name е префрлен во креативец.', ['name' => $user->name]));
    }

    public function destroyUser(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', __('Не можеш да го избришеш сопствениот акаунт.'));
        }

        abort_unless(in_array($user->role, ['creator', 'client'], true), 403);

        $role = $user->role;
        $name = $user->name;

        $user->delete();

        return redirect()->route('admin.users', ['role' => $role])
            ->with('status', __(':name е избришан.', ['name' => $name]));
    }

    public function editCreatorProfile(User $user): View
    {
        abort_unless($user->role === 'creator', 403);

        $creatorProfile = $user->creatorProfile()->firstOrCreate(['user_id' => $user->id]);
        $creatorProfile->load('categories', 'skills');

        return view('admin.creator-edit', [
            'creatorUser' => $user,
            'creatorProfile' => $creatorProfile,
            'categories' => Category::orderBy('id')->get(),
            'skillsByCategory' => Category::orderBy('id')->with('skills')->get()
                ->mapWithKeys(fn ($category) => [$category->id => $category->skills])
                ->filter(fn ($skills) => $skills->isNotEmpty()),
            'selectedCategoryIds' => $creatorProfile->categories->pluck('id')->all(),
            'selectedSkillIds' => $creatorProfile->skills->pluck('id')->all(),
            'countries' => Country::with('cities')->orderBy('id')->get(),
        ]);
    }

    public function updateCreatorProfile(Request $request, User $user, AvatarUploadService $avatarUploadService): RedirectResponse
    {
        abort_unless($user->role === 'creator', 403);

        $creatorProfile = $user->creatorProfile()->firstOrCreate(['user_id' => $user->id]);

        $validated = $request->validate([
            'avatar' => ['nullable', 'image', 'max:9216'],
            'name' => ['required', 'string', 'max:255'],
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
            'country_id' => ['nullable', 'exists:countries,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'onboarding_completed' => ['boolean'],
            'verified' => ['boolean'],
        ], [
            'headline.required' => __('Внеси краток опис.'),
        ]);

        $onboardingCompleted = $request->boolean('onboarding_completed');

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
            'onboarding_completed_at' => $onboardingCompleted ? ($creatorProfile->onboarding_completed_at ?? now()) : null,
            // Can't be verified without a completed profile — matches the rule
            // the normal verification action already enforces.
            'verified' => $onboardingCompleted && $request->boolean('verified'),
        ]);

        $creatorProfile->categories()->sync($validated['category_ids'] ?? []);
        $creatorProfile->skills()->sync($validated['skill_ids'] ?? []);

        $user->update([
            'name' => $validated['name'],
            'country_id' => $validated['country_id'] ?? null,
            'city_id' => $validated['city_id'] ?? null,
        ]);

        if ($request->hasFile('avatar')) {
            $user->update([
                'avatar_url' => $avatarUploadService->store($request->file('avatar')),
            ]);
        }

        return redirect()->route('admin.users', ['role' => 'creator'])
            ->with('status', __(':name е ажуриран.', ['name' => $user->name]));
    }

    public function contactMessages(Request $request): View
    {
        $status = $request->input('status', 'all');
        $status = in_array($status, ['new', 'in_progress', 'resolved'], true) ? $status : 'all';

        $messages = ContactMessage::when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->with('user')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.contact-messages', [
            'messages' => $messages,
            'status' => $status,
            'counts' => [
                'all' => ContactMessage::count(),
                'new' => ContactMessage::where('status', 'new')->count(),
                'in_progress' => ContactMessage::where('status', 'in_progress')->count(),
                'resolved' => ContactMessage::where('status', 'resolved')->count(),
            ],
        ]);
    }

    public function updateContactMessageStatus(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:new,in_progress,resolved'],
        ]);

        $contactMessage->update(['status' => $validated['status']]);

        return back()->with('status', __('Статусот е ажуриран.'));
    }
}
