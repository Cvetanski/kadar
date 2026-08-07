<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\CreatorProfile;
use App\Models\User;
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
        ]);
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
