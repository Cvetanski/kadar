<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Зачувани креативци') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if ($favorites->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('Сѐ уште немаш зачувано креативец.') }}</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($favorites as $creator)
                            <a href="{{ route('creators.show', $creator) }}"
                                class="block border border-gray-200 rounded-md p-4 hover:bg-gray-50">
                                <div class="flex gap-3">
                                    <x-avatar :user="$creator->user" size="w-12 h-12" textSize="text-base" />
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-gray-900">{{ $creator->user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $creator->headline }}</p>
                                        <p class="text-sm text-gray-500 mt-1">{{ $creator->categories->pluck('name')->join(', ') }}</p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ $creator->remote_ok ? __('Remote') : ($creator->user->city?->name ?? $creator->user->country?->name ?? '—') }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
