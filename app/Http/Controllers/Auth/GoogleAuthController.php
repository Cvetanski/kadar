<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AvatarUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(AvatarUploadService $avatarUploadService): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            Auth::login($user);

            return redirect()->route('dashboard');
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            $update = ['google_id' => $googleUser->getId()];

            if (! $user->getRawOriginal('avatar_url') && $googleUser->getAvatar()) {
                $update['avatar_url'] = $avatarUploadService->storeFromUrl($googleUser->getAvatar()) ?? $googleUser->getAvatar();
            }

            $user->update($update);

            Auth::login($user);

            return redirect()->route('dashboard');
        }

        $avatarPath = $googleUser->getAvatar()
            ? $avatarUploadService->storeFromUrl($googleUser->getAvatar()) ?? $googleUser->getAvatar()
            : null;

        $user = User::create([
            'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? $googleUser->getEmail(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'avatar_url' => $avatarPath,
            'password' => null,
            'locale' => app()->getLocale(),
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        return redirect()->route('choose-role');
    }
}
