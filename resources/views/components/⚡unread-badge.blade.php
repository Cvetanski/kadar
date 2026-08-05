<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    #[Computed]
    public function count(): int
    {
        return Auth::user()->unreadMessagesCount();
    }

    #[On('messages-read')]
    public function refresh(): void
    {
        //
    }
};
?>

<span wire:poll.5s>
    @if ($this->count > 0)
        <span {{ $attributes->merge(['class' => 'inline-flex items-center justify-center min-w-5 h-5 px-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full']) }}>
            {{ $this->count > 9 ? '9+' : $this->count }}
        </span>
    @endif
</span>
