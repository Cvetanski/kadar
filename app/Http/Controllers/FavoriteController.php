<?php

namespace App\Http\Controllers;

use App\Models\CreatorProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function toggle(Request $request, CreatorProfile $creatorProfile): RedirectResponse
    {
        abort_if($request->user()->id === $creatorProfile->user_id, 403);

        $request->user()->favorites()->toggle($creatorProfile->id);

        return back();
    }

    public function index(Request $request): View
    {
        $favorites = $request->user()->favorites()
            ->with(['user.country', 'user.city', 'categories'])
            ->get();

        return view('favorites.index', ['favorites' => $favorites]);
    }
}
