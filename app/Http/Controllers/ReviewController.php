<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Contract $contract): RedirectResponse
    {
        $user = $request->user();

        abort_unless($contract->status === 'completed', 403);

        if ($user->id === $contract->client_id) {
            $revieweeId = $contract->creatorProfile->user_id;
        } elseif ($contract->creatorProfile && $user->id === $contract->creatorProfile->user_id) {
            $revieweeId = $contract->client_id;
        } else {
            abort(403);
        }

        if (Review::where('contract_id', $contract->id)->where('reviewer_id', $user->id)->exists()) {
            return back()->withErrors(['review' => __('Веќе имаш оставено ревју за овој договор.')]);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        Review::create([
            'contract_id' => $contract->id,
            'reviewer_id' => $user->id,
            'reviewee_id' => $revieweeId,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return redirect()->route('projects.show', $contract->project)
            ->with('status', __('Ревјуто е зачувано.'));
    }
}
