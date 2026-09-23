<?php

namespace App\Http\Controllers;

use App\Services\AvatarUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientWelcomeController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->role !== 'client') {
            return redirect()->route('dashboard');
        }

        return view('client-welcome.index');
    }

    public function store(Request $request, AvatarUploadService $avatarUploadService): RedirectResponse
    {
        $user = $request->user();

        if ($user->role !== 'client') {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'avatar' => ['nullable', 'image', 'max:9216'],
        ]);

        if ($request->hasFile('avatar')) {
            $user->update(['avatar_url' => $avatarUploadService->store($request->file('avatar'))]);
        }

        return redirect()->route('dashboard');
    }
}
