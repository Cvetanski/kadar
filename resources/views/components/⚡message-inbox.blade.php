<?php

use App\Models\Conversation;
use App\Models\Proposal;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public ?int $selectedConversationId = null;

    public string $newMessageBody = '';

    public string $search = '';

    public int $composeInstanceKey = 0;

    public function mount(?int $selectedConversationId = null): void
    {
        if (! $selectedConversationId) {
            return;
        }

        $conversation = Conversation::find($selectedConversationId);

        if ($conversation && $conversation->participants->contains('id', Auth::id())) {
            $this->selectedConversationId = $selectedConversationId;
            $this->markAsRead($conversation);
        }
    }

    #[Computed]
    public function conversations()
    {
        $userId = Auth::id();

        $conversations = Auth::user()->conversations()
            ->with(['participants', 'latestMessage'])
            ->orderByDesc('updated_at')
            ->get();

        if ($this->search === '') {
            return $conversations;
        }

        $needle = mb_strtolower($this->search);

        return $conversations->filter(function ($conversation) use ($userId, $needle) {
            $other = $conversation->participants->firstWhere('id', '!=', $userId);

            return $other && str_contains(mb_strtolower($other->name), $needle);
        });
    }

    #[Computed]
    public function selectedConversation()
    {
        if (! $this->selectedConversationId) {
            return null;
        }

        return Conversation::with(['participants', 'messages.sender', 'project.categories'])->find($this->selectedConversationId);
    }

    #[Computed]
    public function otherParticipant()
    {
        return $this->selectedConversation?->participants->firstWhere('id', '!=', Auth::id());
    }

    /**
     * The pending proposal the current user (as the project's client) can
     * accept or reject directly from this conversation, if any.
     */
    #[Computed]
    public function relevantProposal()
    {
        $conversation = $this->selectedConversation;
        $project = $conversation?->project;

        if (! $project || $project->client_id !== Auth::id()) {
            return null;
        }

        $creatorProfile = $this->otherParticipant?->creatorProfile;

        if (! $creatorProfile) {
            return null;
        }

        return Proposal::where('project_id', $project->id)
            ->where('creator_profile_id', $creatorProfile->id)
            ->where('status', 'pending')
            ->first();
    }

    public function selectConversation(int $conversationId): void
    {
        $conversation = Conversation::findOrFail($conversationId);

        abort_unless($conversation->participants->contains('id', Auth::id()), 403);

        $this->selectedConversationId = $conversationId;
        $this->newMessageBody = '';
        $this->markAsRead($conversation);
        $this->forgetComputed();
        $this->dispatch('scroll-chat-bottom');
    }

    public function backToList(): void
    {
        $this->selectedConversationId = null;
    }

    public function sendMessage(): void
    {
        $this->validate([
            'newMessageBody' => ['required', 'string', 'max:2000'],
        ]);

        $conversation = $this->selectedConversation;

        abort_unless($conversation, 404);

        $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'body' => $this->newMessageBody,
        ]);

        $conversation->touch();

        $this->newMessageBody = '';
        $this->composeInstanceKey++;
        $this->forgetComputed();
        $this->dispatch('scroll-chat-bottom');
    }

    public function poll(): void
    {
        if ($this->selectedConversationId) {
            $conversation = Conversation::find($this->selectedConversationId);

            if ($conversation) {
                $this->markAsRead($conversation);
            }
        }

        $this->forgetComputed();
    }

    public function acceptProposal(): void
    {
        $proposal = $this->relevantProposal;

        abort_unless($proposal, 404);

        try {
            $proposal->accept();
        } catch (ValidationException $e) {
            $this->addError('proposal', $e->validator->errors()->first());

            return;
        }

        $this->forgetComputed();
    }

    public function rejectProposal(): void
    {
        $proposal = $this->relevantProposal;

        abort_unless($proposal, 404);

        try {
            $proposal->reject();
        } catch (ValidationException $e) {
            $this->addError('proposal', $e->validator->errors()->first());

            return;
        }

        $this->forgetComputed();
    }

    /**
     * #[Computed] properties are memoized for the lifetime of a single Livewire
     * request. Without this, an action that both mutates data (e.g. creating a
     * message) and re-renders in the same round-trip would show stale data
     * until the next poll, since the cached value from before the mutation
     * would still be returned.
     */
    private function forgetComputed(): void
    {
        unset($this->conversations, $this->selectedConversation, $this->otherParticipant, $this->relevantProposal);
    }

    private function markAsRead(Conversation $conversation): void
    {
        $conversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->dispatch('messages-read');
    }

    public function listTimestamp(Carbon $time): string
    {
        if ($time->isToday()) {
            return $time->format('H:i');
        }

        if ($time->isYesterday()) {
            return __('вчера');
        }

        return $time->format('d.m.Y');
    }
};
?>

<div wire:poll.2s="poll">
    <div class="msg-shell">
        <div class="msg-sidebar {{ $this->selectedConversationId ? 'has-selected' : '' }}">
            <div class="msg-sidebar-search">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Пребарај по име...') }}">
            </div>

            <div class="msg-list">
                @forelse ($this->conversations as $conversation)
                    @php
                        $other = $conversation->participants->firstWhere('id', '!=', auth()->id());
                        $last = $conversation->latestMessage;
                        $isUnread = $last && $last->sender_id !== auth()->id() && ! $last->read_at;
                    @endphp
                    <button type="button" wire:key="conversation-{{ $conversation->id }}"
                        wire:click="selectConversation({{ $conversation->id }})"
                        class="msg-list-item {{ $conversation->id === $this->selectedConversationId ? 'active' : '' }}">
                        @if ($other)
                            <x-avatar :user="$other" size="w-10 h-10" textSize="text-sm" />
                        @endif
                        <div class="min-w-0 flex-1" style="text-align:left;">
                            <p class="msg-list-name">{{ $other?->name ?? __('Непознат корисник') }}</p>
                            <p class="msg-list-preview">{{ $last?->body ?? __('Нема пораки сѐ уште.') }}</p>
                        </div>
                        <div class="msg-list-meta">
                            @if ($last)
                                <span class="msg-list-time">{{ $this->listTimestamp($last->created_at) }}</span>
                            @endif
                            @if ($isUnread)
                                <span class="msg-list-dot"></span>
                            @endif
                        </div>
                    </button>
                @empty
                    <div class="msg-empty-list">{{ __('Сѐ уште немаш разговори.') }}</div>
                @endforelse
            </div>
        </div>

        <div class="msg-chat {{ $this->selectedConversationId ? 'is-active' : '' }}">
            @if ($this->selectedConversation)
                <div class="msg-chat-header">
                    <button type="button" class="back-btn" wire:click="backToList">←</button>
                    <x-avatar :user="$this->otherParticipant" size="w-9 h-9" textSize="text-sm" />
                    <div class="min-w-0">
                        <p class="msg-chat-name">{{ $this->otherParticipant?->name }}</p>
                        <p class="msg-chat-role">
                            @if ($this->otherParticipant?->creatorProfile)
                                {{ $this->otherParticipant->creatorProfile->headline ?: __('Креативец') }}
                                · <a href="{{ route('creators.show', $this->otherParticipant->creatorProfile) }}">{{ __('Погледни профил →') }}</a>
                            @else
                                {{ __('Клиент') }}
                            @endif
                        </p>
                    </div>
                </div>

                @if ($this->selectedConversation->project)
                    @php $conversationProject = $this->selectedConversation->project; @endphp
                    <div class="msg-project-banner">
                        <div class="msg-project-icon">{{ $conversationProject->categories->first()?->icon ?? '📁' }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="msg-project-title">{{ $conversationProject->title }}</p>
                            <p class="msg-project-meta">
                                @if ($conversationProject->budget_min || $conversationProject->budget_max)
                                    {{ $conversationProject->budget_min ?? '?' }}–{{ $conversationProject->budget_max ?? '?' }} EUR
                                @else
                                    {{ __('Цена по договор') }}
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('projects.show', $conversationProject) }}" class="msg-project-link">{{ __('Погледни оглас →') }}</a>
                    </div>
                @endif

                @if ($this->relevantProposal)
                    <div class="msg-proposal-actions">
                        <div>
                            <p class="msg-proposal-label">{{ __('Понудена цена') }}</p>
                            <p class="msg-proposal-price">{{ $this->relevantProposal->price }} EUR</p>
                        </div>
                        <div class="msg-proposal-buttons">
                            <button type="button" class="msg-btn-reject" wire:click="rejectProposal">{{ __('Одбиј') }}</button>
                            <button type="button" class="msg-btn-accept" wire:click="acceptProposal">{{ __('Прифати') }}</button>
                        </div>
                    </div>
                    @error('proposal')
                        <div style="color:#DC2626;font-size:12.5px;padding:8px 20px;background:#FEF2F2;">{{ $message }}</div>
                    @enderror
                @endif

                <div class="msg-thread" x-data x-init="$el.scrollTop = $el.scrollHeight"
                    x-on:scroll-chat-bottom.window="setTimeout(() => $el.scrollTop = $el.scrollHeight, 50)">
                    @php $lastDate = null; @endphp
                    @forelse ($this->selectedConversation->messages->sortBy('created_at') as $chatMessage)
                        @php
                            $messageDate = $chatMessage->created_at->toDateString();
                            $showSeparator = $messageDate !== $lastDate;
                            $lastDate = $messageDate;
                        @endphp
                        @if ($showSeparator)
                            <div class="msg-date-sep">
                                {{ $chatMessage->created_at->isToday() ? __('Денес') : ($chatMessage->created_at->isYesterday() ? __('Вчера') : $chatMessage->created_at->format('d.m.Y')) }}
                            </div>
                        @endif
                        @if ($chatMessage->type === 'system')
                            <div class="msg-system" wire:key="message-{{ $chatMessage->id }}">{{ $chatMessage->body }}</div>
                        @else
                            <div class="msg-row {{ $chatMessage->sender_id === auth()->id() ? 'mine' : 'theirs' }}" wire:key="message-{{ $chatMessage->id }}">
                                <div class="msg-bubble">
                                    <p style="white-space:pre-line;">{{ $chatMessage->body }}</p>
                                    <p class="msg-bubble-time">{{ $chatMessage->created_at->format('H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    @empty
                        <p style="text-align:center;color:#9AA0AB;font-size:13px;">{{ __('Сѐ уште нема пораки во овој разговор.') }}</p>
                    @endforelse
                </div>

                <form wire:submit.prevent="sendMessage" class="msg-compose">
                    <textarea wire:key="compose-{{ $this->selectedConversationId }}-{{ $composeInstanceKey }}" wire:model="newMessageBody" rows="1" placeholder="{{ __('Напиши порака...') }}"></textarea>
                    <button type="submit" class="msg-send-btn">{{ __('Прати') }}</button>
                </form>
                @error('newMessageBody')
                    <div style="color:#DC2626;font-size:12.5px;padding:0 20px 10px;">{{ $message }}</div>
                @enderror
            @else
                <div class="msg-empty-chat">{{ __('Избери разговор од листата лево') }}</div>
            @endif
        </div>
    </div>
</div>
