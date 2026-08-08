<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public bool $dismissed = false;

    public function mount(): void
    {
        $this->dismissed = (bool) Auth::user()->video_intro_dismissed;
    }

    public function dismiss(): void
    {
        Auth::user()->update(['video_intro_dismissed' => true]);

        $this->dismissed = true;
    }
};
?>

<div>
    @unless ($dismissed)
        <div class="relative mb-6 rounded-xl border border-blue-100 bg-blue-50 p-5 sm:p-6">
            <button
                type="button"
                wire:click="dismiss"
                class="absolute top-3 right-3 flex h-7 w-7 items-center justify-center rounded-full text-gray-400 hover:bg-white hover:text-gray-600 transition"
                aria-label="{{ __('Затвори') }}"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <p class="mb-4 pr-8 text-sm sm:text-base font-semibold text-gray-800">
                {{ __('👋 Прв пат тука? Погледни како работи CreatorSpot') }}
            </p>

            <div class="max-w-xl">
                <x-video-embed video-id="QZFgwu7eDp0" :title="__('Како функционира CreatorSpot')" />
            </div>
        </div>
    @endunless
</div>
