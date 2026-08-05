<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function complete(Request $request, Contract $contract): RedirectResponse
    {
        abort_unless($request->user()->id === $contract->client_id, 403);
        abort_unless($contract->status === 'active', 404);

        $contract->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $contract->project->update(['status' => 'completed']);

        return redirect()->route('projects.show', $contract->project)
            ->with('status', __('Договорот е означен како завршен.'));
    }
}
