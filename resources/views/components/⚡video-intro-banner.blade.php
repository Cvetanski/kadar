<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public bool $dismissed = false;

    public bool $isCreator = false;

    public bool $isClient = false;

    public function mount(): void
    {
        $this->dismissed = (bool) Auth::user()->video_intro_dismissed;
        $this->isCreator = Auth::user()->role === 'creator';
        $this->isClient = Auth::user()->role === 'client';
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="mb-2 text-sm font-semibold text-gray-700">{{ __('Како функционира CreatorSpot') }}</p>
                    <x-video-embed video-id="QZFgwu7eDp0" :title="__('Како функционира CreatorSpot')" />
                </div>

                @if ($isCreator)
                    <div>
                        <p class="mb-2 text-sm font-semibold text-gray-700">{{ __('Како да го поставиш профилот') }}</p>
                        <x-video-embed video-id="rnCT3fibMf0" :title="__('Како да го поставиш профилот')" />
                    </div>
                @elseif ($isClient)
                    <div>
                        <p class="mb-2 text-sm font-semibold text-gray-700">{{ __('Како да ангажираш креативец') }}</p>
                        <x-video-embed video-id="EbT87_K2_WM" :title="__('Како да ангажираш креативец')" />
                    </div>
                @endif
            </div>
        </div>
    @endunless
</div>
