<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CreatorProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ChooseRoleController extends Controller
{
    public function create(): View
    {
        return view('auth.choose-role');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['client', 'creator'])],
        ]);

        $user = $request->user();
        $user->update(['role' => $validated['role']]);

        if ($validated['role'] === 'creator') {
            CreatorProfile::firstOrCreate(['user_id' => $user->id]);

            return redirect()->route('onboarding');
        }

        return redirect()->route('dashboard');
    }
}
