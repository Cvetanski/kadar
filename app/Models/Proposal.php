<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Proposal extends Model
{
    protected $fillable = [
        'project_id',
        'creator_profile_id',
        'message',
        'price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creatorProfile(): BelongsTo
    {
        return $this->belongsTo(CreatorProfile::class);
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class);
    }

    /**
     * Accept this proposal, rejecting every other pending proposal on the
     * same project and creating the resulting contract — all inside a
     * locked transaction so a double-accept race can't create two contracts.
     */
    public function accept(): Contract
    {
        return DB::transaction(function () {
            $project = Project::whereKey($this->project_id)->lockForUpdate()->firstOrFail();

            if ($project->status !== 'open') {
                throw ValidationException::withMessages([
                    'proposal' => __('Овој проект веќе има доделен договор.'),
                ]);
            }

            $proposal = self::whereKey($this->id)->lockForUpdate()->firstOrFail();

            if ($proposal->status !== 'pending') {
                throw ValidationException::withMessages([
                    'proposal' => __('Оваа понуда веќе не е активна.'),
                ]);
            }

            $proposal->update(['status' => 'accepted']);

            self::where('project_id', $project->id)
                ->where('id', '!=', $proposal->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            $contract = Contract::create([
                'project_id' => $project->id,
                'proposal_id' => $proposal->id,
                'client_id' => $project->client_id,
                'creator_profile_id' => $proposal->creator_profile_id,
                'agreed_price' => $proposal->price,
                'status' => 'active',
                'started_at' => now(),
            ]);

            $project->update(['status' => 'in_progress']);

            $this->postAcceptanceSystemMessage($project, $proposal);

            return $contract;
        });
    }

    private function postAcceptanceSystemMessage(Project $project, self $proposal): void
    {
        $client = $project->client;
        $creator = $proposal->creatorProfile->user;

        $conversation = Conversation::findOrCreateBetween($creator, $client, $project->id);

        $conversation->messages()->create([
            'sender_id' => $client->id,
            'body' => __('🎉 Честитки! Понудата е прифатена — успешна соработка на').' „'.$project->title.'“.',
            'type' => 'system',
        ]);

        $conversation->touch();
    }

    public function reject(): void
    {
        DB::transaction(function () {
            $proposal = self::whereKey($this->id)->lockForUpdate()->firstOrFail();

            if ($proposal->status !== 'pending') {
                throw ValidationException::withMessages([
                    'proposal' => __('Оваа понуда веќе не е активна.'),
                ]);
            }

            $proposal->update(['status' => 'rejected']);
        });
    }
}
