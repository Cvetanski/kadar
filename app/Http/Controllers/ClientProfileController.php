<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class ClientProfileController extends Controller
{
    public function show(User $user): View
    {
        abort_unless($user->role === 'client', 404);

        $reviews = $user->reviewsReceived()->with('reviewer')->latest()->get();

        return view('clients.show', [
            'client' => $user,
            'reviews' => $reviews,
            'averageRating' => $reviews->isNotEmpty() ? $reviews->avg('rating') : 0,
        ]);
    }
}
