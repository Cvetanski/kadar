<x-app-layout :title="__('Како функционира CreatorSpot')">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Како функционира CreatorSpot') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 sm:p-8">
                <p class="mb-6 text-sm sm:text-base text-gray-600">
                    {{ __('Погледни како да го искористиш максимумот од CreatorSpot.') }}
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="mb-2 text-sm font-semibold text-gray-700">{{ __('Како функционира CreatorSpot') }}</p>
                        <x-video-embed video-id="QZFgwu7eDp0" :title="__('Како функционира CreatorSpot')" />
                    </div>

                    @if (Auth::user()->role === 'creator')
                        <div>
                            <p class="mb-2 text-sm font-semibold text-gray-700">{{ __('Како да го поставиш профилот') }}</p>
                            <x-video-embed video-id="rnCT3fibMf0" :title="__('Како да го поставиш профилот')" />
                        </div>
                    @elseif (Auth::user()->role === 'client')
                        <div>
                            <p class="mb-2 text-sm font-semibold text-gray-700">{{ __('Како да ангажираш креативец') }}</p>
                            <x-video-embed video-id="EbT87_K2_WM" :title="__('Како да ангажираш креативец')" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
