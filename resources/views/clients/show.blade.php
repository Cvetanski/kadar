<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $client->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex gap-4 items-center">
                    <x-avatar :user="$client" size="w-20 h-20" textSize="text-2xl" />
                    <div>
                        <p class="text-lg font-medium text-gray-900">{{ $client->name }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ __('Клиент на CreatorSpot') }} &middot; {{ __('се приклучи') }} {{ $client->created_at->format('d.m.Y') }}</p>
                    </div>
                </div>
            </div>

            <div id="reviews" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Ревјуа') }}</h3>
                    <x-star-rating :rating="$reviews->isNotEmpty() ? $averageRating : null" :count="$reviews->count()" size="text-base" />
                </div>

                @if ($reviews->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('Сѐ уште нема ревјуа.') }}</p>
                @else
                    <div class="space-y-4">
                        @foreach ($reviews as $review)
                            <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                <div class="flex justify-between items-center">
                                    <p class="font-medium text-gray-900">{{ $review->reviewer->name }}</p>
                                    <p class="text-sm text-gray-500">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</p>
                                </div>
                                @if ($review->comment)
                                    <p class="text-sm text-gray-700 mt-1">{{ $review->comment }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-1">{{ $review->created_at->format('d.m.Y') }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
