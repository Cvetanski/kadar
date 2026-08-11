<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public ?int $previousCount = null;

    public function mount(): void
    {
        $this->previousCount = $this->count;
    }

    #[Computed]
    public function count(): int
    {
        return Auth::user()->unreadMessagesCount();
    }

    public function poll(): void
    {
        $current = $this->count;

        if ($this->previousCount !== null && $current > $this->previousCount) {
            $this->dispatch('new-message-received');
        }

        $this->previousCount = $current;
    }

    #[On('messages-read')]
    public function refresh(): void
    {
        $this->previousCount = $this->count;
    }
};
?>

<span wire:poll.3s.keep-alive="poll">
    @if ($this->count > 0)
        <span {{ $attributes->merge(['class' => 'inline-flex items-center justify-center min-w-5 h-5 px-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full']) }}>
            {{ $this->count > 9 ? '9+' : $this->count }}
        </span>
    @endif
</span>
