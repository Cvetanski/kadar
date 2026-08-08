<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Здраво,') }} {{ Auth::user()->name }} 👋</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('partials.creator-fancy-styles')
            <div class="kf-wrap">

                <livewire:video-intro-banner />

                <div class="kf-stats">
                    <div class="kf-stat kf-accent">
                        <div class="kf-num">{{ $stats['open_projects'] }}</div>
                        <div class="kf-label">{{ __('Отворени проекти') }}</div>
                    </div>
                    <div class="kf-stat kf-accent">
                        <div class="kf-num">{{ $stats['active_contracts'] }}</div>
                        <div class="kf-label">{{ __('Активни договори') }}</div>
                    </div>
                    <div class="kf-stat kf-verified">
                        <div class="kf-num">{{ $stats['completed_contracts'] }}</div>
                        <div class="kf-label">{{ __('Завршени проекти') }}</div>
                    </div>
                    <div class="kf-stat">
                        <div class="kf-num">{{ $stats['favorites'] }}</div>
                        <div class="kf-label">{{ __('Зачувани креативци') }}</div>
                    </div>
                </div>

                <div class="kf-section">
                    <div class="kf-section-title">
                        <h3>{{ __('Препорачани креативци') }}</h3>
                        <a href="{{ route('creators.index') }}">{{ __('Погледни ги сите →') }}</a>
                    </div>

                    @if ($recommendedCreators->isEmpty())
                        <div class="kf-empty">
                            {{ __('Наскоро првите верификувани креативци во твоите категории.') }}
                            <a href="{{ route('creators.index') }}" style="color:#0B6FE0;font-weight:700;">{{ __('Разгледај ги сите креативци →') }}</a>
                        </div>
                    @else
                        <div class="kf-grid">
                            @foreach ($recommendedCreators as $creator)
                                <a href="{{ route('creators.show', $creator) }}" class="kf-card">
                                    <div class="flex gap-3">
                                        <x-avatar :user="$creator->user" size="w-12 h-12" textSize="text-base" />
                                        <div class="min-w-0 flex-1">
                                            <p class="kf-card-title">{{ $creator->user->name }}</p>
                                            <p class="kf-card-meta" style="margin-bottom:6px;">{{ $creator->headline }}</p>
                                            <x-star-rating :rating="$creator->reviews_avg_rating" :count="$creator->reviews_count" />
                                        </div>
                                    </div>
                                    <div class="kf-card-foot">
                                        <span class="kf-card-meta" style="margin-bottom:0;">
                                            {{ $creator->remote_ok ? __('Remote') : ($creator->user->city?->name ?? $creator->user->country?->name ?? '—') }}
                                        </span>
                                        <span class="kf-budget">{{ $creator->hourly_rate ? $creator->hourly_rate.' EUR/ч' : '—' }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
