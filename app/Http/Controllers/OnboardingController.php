<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->role !== 'creator') {
            return redirect()->route('dashboard');
        }

        if ($user->creatorProfile?->onboarding_completed_at) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.index');
    }
}
