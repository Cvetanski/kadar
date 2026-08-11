@php
    $seoLocation = $creatorProfile->remote_ok
        ? __('Remote')
        : ($creatorProfile->user->city?->name ?? $creatorProfile->user->country?->name);
    $seoRole = $creatorProfile->categories->first()?->name ?? __('Креативец');
    $seoTitle = $creatorProfile->user->name.' - '.$seoRole.($seoLocation ? ' '.__('во').' '.$seoLocation : '');
    $seoDescription = $creatorProfile->bio
        ? \Illuminate\Support\Str::limit(strip_tags($creatorProfile->bio), 155)
        : ($creatorProfile->headline ?: $seoTitle);
    $seoRoleLower = mb_strtolower($seoRole);
    $seoKeywords = collect([
        $seoRoleLower.' Македонија',
        $seoRoleLower.' Скопје',
        'hire '.$seoRoleLower.' Macedonia',
        'freelance '.$seoRoleLower.' Macedonia',
        'креативци Македонија',
        'creative professionals Macedonia',
    ])->implode(', ');
@endphp

<x-app-layout :title="$seoTitle" :description="$seoDescription" :keywords="$seoKeywords" :image="$creatorProfile->user->avatar_url" type="profile">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $creatorProfile->user->name }}</h2>
    </x-slot>

    <script type="application/ld+json">
        {!! json_encode(array_filter([
            '@@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $creatorProfile->user->name,
            'url' => route('creators.show', $creatorProfile),
            'image' => $creatorProfile->user->avatar_url,
            'jobTitle' => $creatorProfile->headline,
            'description' => $creatorProfile->bio,
            'address' => ($creatorProfile->user->city || $creatorProfile->user->country) ? array_filter([
                '@type' => 'PostalAddress',
                'addressLocality' => $creatorProfile->user->city?->name,
                'addressCountry' => $creatorProfile->user->country?->name,
            ]) : null,
            'knowsAbout' => $creatorProfile->categories->pluck('name')->all() ?: null,
            'makesOffer' => $creatorProfile->hourly_rate ? [
                '@type' => 'Offer',
                'priceCurrency' => 'EUR',
                'price' => (string) $creatorProfile->hourly_rate,
                'description' => __('Час. тарифа'),
            ] : null,
            'aggregateRating' => $reviews->isNotEmpty() ? [
                '@type' => 'AggregateRating',
                'ratingValue' => round($averageRating, 1),
                'reviewCount' => $reviews->count(),
                'bestRating' => 5,
                'worstRating' => 1,
            ] : null,
        ]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>

    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @include('partials.creator-fancy-styles')

            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-md p-4">{{ session('status') }}</div>
            @endif

            <div class="kf-card">
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
                    <div class="kf-social-links mt-6">
                        @if ($creatorProfile->instagram_url)
                            <a href="{{ $creatorProfile->instagram_url }}" target="_blank" class="kf-social-link">
                                <span class="kf-social-icon kf-instagram" style="width:24px;height:24px;border-radius:7px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></span> {{ __('Instagram') }}
                            </a>
                        @endif
                        @if ($creatorProfile->facebook_url)
                            <a href="{{ $creatorProfile->facebook_url }}" target="_blank" class="kf-social-link">
                                <span class="kf-social-icon kf-facebook" style="width:24px;height:24px;border-radius:7px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg></span> {{ __('Facebook') }}
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

            @if ($isOwnProfile)
                <livewire:portfolio-manager :creator-profile="$creatorProfile" :collapsible="true" />
            @elseif ($creatorProfile->portfolioItems->isNotEmpty())
                <div class="kf-card">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Портфолио') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach ($creatorProfile->portfolioItems as $item)
                            <div>
                                @if ($item->title)
                                    <p class="text-sm font-medium text-gray-900 mb-2">{{ $item->title }}</p>
                                @endif
                                <x-portfolio-preview :item="$item" />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div id="reviews" class="kf-card">
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
