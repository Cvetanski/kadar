<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Најди Работа') }}</h2>
            @if (Auth::user()->role === 'creator')
                <a href="{{ route('saved-projects.index') }}" class="text-sm text-indigo-600 hover:underline">{{ __('Зачувани огласи →') }}</a>
            @endif
        </div>
    </x-slot>

    @include('partials.creator-fancy-styles')
    @include('partials.browse-styles')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="br-wrap">
                <livewire:browse-projects />
            </div>
        </div>
    </div>
</x-app-layout>
