<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCreatorOnboarded
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'creator') {
            $profile = $user->creatorProfile;

            if (! $profile || ! $profile->onboarding_completed_at) {
                return redirect()->route('onboarding');
            }
        }

        return $next($request);
    }
}
