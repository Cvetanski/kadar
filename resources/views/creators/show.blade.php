@php
    $seoLocation = $creatorProfile->remote_ok
        ? __('Remote')
        : ($creatorProfile->user->city?->name ?? $creatorProfile->user->country?->name);
    $seoRole = $creatorProfile->categories->first()?->name ?? __('Креативец');
    $seoTitle = $creatorProfile->user->name.' - '.$seoRole.($seoLocation ? ' '.__('во').' '.$seoLocation : '');
    $seoDescription = $creatorProfile->bio
        ? \Illuminate\Support\Str::limit(strip_tags($creatorProfile->bio), 155)
        : ($creatorProfile->headline ?: $seoTitle);
@endphp

<x-app-layout :title="$seoTitle" :description="$seoDescription" :image="$creatorProfile->user->avatar_url" type="profile">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $creatorProfile->user->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-md p-4">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                    <div class="flex gap-4">
                        <x-avatar :user="$creatorProfile->user" size="w-20 h-20" textSize="text-2xl" />
                        <div class="min-w-0">
                            <p class="text-lg font-medium text-gray-900">
                                {{ $creatorProfile->headline }}
                                @if ($creatorProfile->verified)
                                    <span class="ms-1 text-xs font-semibold bg-blue-100 text-blue-700 px-2 py-1 rounded-full align-middle">{{ __('Верифициран') }}</span>
                                @endif
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $creatorProfile->categories->pluck('name')->join(', ') }}
                            </p>
                        </div>
                    </div>

                    @if ($isOwnProfile)
                        <a href="{{ route('creators.edit', $creatorProfile) }}">
                            <x-secondary-button type="button">✏️ {{ __('Уреди профил') }}</x-secondary-button>
                        </a>
                    @else
                        <div class="flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('favorites.toggle', $creatorProfile) }}">
                                @csrf
                                <x-secondary-button type="submit">
                                    {{ $isFavorited ? __('Зачувано ✓') : __('Зачувај') }}
                                </x-secondary-button>
                            </form>
                            <form method="POST" action="{{ route('messages.start', $creatorProfile) }}">
                                @csrf
                                <x-primary-button type="submit">{{ __('Прати порака') }}</x-primary-button>
                            </form>
                        </div>
                    @endif
                </div>

                <p class="text-sm text-gray-700 mt-4 whitespace-pre-line">{{ $creatorProfile->bio }}</p>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-6 text-sm">
                    <div>
                        <p class="text-gray-500">{{ __('Час. тарифа') }}</p>
                        <p class="font-medium text-gray-900">{{ $creatorProfile->hourly_rate ? $creatorProfile->hourly_rate.' EUR' : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">{{ __('Искуство') }}</p>
                        <p class="font-medium text-gray-900">{{ $creatorProfile->experience_years }} {{ __('год.') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">{{ __('Локација') }}</p>
                        <p class="font-medium text-gray-900">
                            {{ $creatorProfile->remote_ok ? __('Remote') : ($creatorProfile->user->city?->name ?? $creatorProfile->user->country?->name ?? '—') }}
                        </p>
                    </div>
                </div>

                @if ($creatorProfile->skills->isNotEmpty())
                    <div class="mt-6">
                        <p class="text-sm text-gray-500 mb-1">{{ __('Вештини') }}</p>
                        <p class="text-sm text-gray-900">{{ $creatorProfile->skills->pluck('name')->join(', ') }}</p>
                    </div>
                @endif

                @if ($creatorProfile->instagram_url || $creatorProfile->facebook_url || $creatorProfile->website_url)
                    @include('partials.creator-fancy-styles')
                    <div class="kf-social-links mt-6">
                        @if ($creatorProfile->instagram_url)
                            <a href="{{ $creatorProfile->instagram_url }}" target="_blank" class="kf-social-link">
                                <span class="kf-social-icon kf-instagram" style="width:24px;height:24px;font-size:12px;border-radius:7px;">📷</span> {{ __('Instagram') }}
                            </a>
                        @endif
                        @if ($creatorProfile->facebook_url)
                            <a href="{{ $creatorProfile->facebook_url }}" target="_blank" class="kf-social-link">
                                <span class="kf-social-icon kf-facebook" style="width:24px;height:24px;font-size:12px;border-radius:7px;">📘</span> {{ __('Facebook') }}
                            </a>
                        @endif
                        @if ($creatorProfile->website_url)
                            <a href="{{ $creatorProfile->website_url }}" target="_blank" class="kf-social-link">
                                <span class="kf-social-icon kf-website" style="width:24px;height:24px;font-size:12px;border-radius:7px;">🌐</span> {{ __('Веб-сајт') }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            @if ($creatorProfile->portfolioItems->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Портфолио') }}</h3>
                    <div class="space-y-3">
                        @foreach ($creatorProfile->portfolioItems as $item)
                            <div class="border border-gray-200 rounded-md p-3">
                                <p class="font-medium text-gray-900">{{ $item->title ?: __('Без наслов') }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $item->media_type === 'video' ? __('Видео') : __('Слика') }} —
                                    <a href="{{ $item->media_url }}" target="_blank" class="underline break-all">{{ $item->media_url }}</a>
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

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
