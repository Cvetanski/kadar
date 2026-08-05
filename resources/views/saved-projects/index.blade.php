<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Зачувани огласи') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if ($savedProjects->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('Сѐ уште немаш зачувано оглас.') }}</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($savedProjects as $project)
                            <a href="{{ route('projects.show', $project) }}"
                                class="block border border-gray-200 rounded-md p-4 hover:bg-gray-50">
                                <div class="flex justify-between items-start gap-2">
                                    <p class="font-medium text-gray-900">{{ $project->title }}</p>
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $project->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $project->status === 'open' ? __('Отворен') : __('Затворен') }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">{{ $project->categories->pluck('name')->join(', ') }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    @if ($project->budget_min || $project->budget_max)
                                        {{ $project->budget_min ?? '?' }}–{{ $project->budget_max ?? '?' }} EUR
                                    @else
                                        {{ __('Цена по договор') }}
                                    @endif
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    📍 {{ $project->remote_ok ? __('Remote') : ($project->city?->name ?? $project->country?->name ?? '—') }}
                                </p>
                                <p class="text-xs text-gray-400 mt-2">{{ __('Клиент:') }} {{ $project->client->name }}</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
