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

                <x-video-embed video-id="QZFgwu7eDp0" :title="__('Како функционира CreatorSpot')" />
            </div>
        </div>
    </div>
</x-app-layout>
