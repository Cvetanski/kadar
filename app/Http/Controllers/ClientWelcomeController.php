<?php

namespace App\Http\Controllers;

use App\Models\Country;
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

        $countries = Country::with('cities')->orderBy('id')->get();

        return view('client-welcome.index', compact('countries'));
    }

    public function store(Request $request, AvatarUploadService $avatarUploadService): RedirectResponse
    {
        $user = $request->user();

        if ($user->role !== 'client') {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'avatar' => ['nullable', 'image', 'max:9216'],
            'city_id' => ['nullable', 'exists:cities,id'],
        ]);

        $update = ['city_id' => $validated['city_id'] ?? null];

        if ($request->hasFile('avatar')) {
            $update['avatar_url'] = $avatarUploadService->store($request->file('avatar'));
        }

        $user->update($update);

        return redirect()->route('dashboard');
    }
}
