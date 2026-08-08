<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Здраво,') }} {{ Auth::user()->name }} 👋</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('partials.creator-fancy-styles')
            <div class="kf-wrap">

                <livewire:video-intro-banner />

                @if ($profile && $profile->onboarding_completed_at && ! $profile->verified)
                    <div class="kf-card" style="margin-bottom:24px;border-color:#FDE68A;background:#FFFBEB;">
                        <p class="kf-card-title" style="margin-bottom:2px;">🕒 {{ __('Твојот профил чека верификација') }}</p>
                        <p class="kf-card-meta" style="margin-bottom:0;">{{ __('По поднесувањето, профилот чека рачна верификација од нашиот тим, обично до 24 часа. Верифицираните профили имаат поголема шанса да бидат ангажирани од клиенти.') }}</p>
                        @unless (Auth::user()->avatar_url)
                            <p class="text-xs font-semibold mt-2" style="color:#B45309;">{{ __('Задолжително додади профилна слика, без неа профилот не може да биде верификуван.') }}</p>
                        @endunless
                    </div>
                @endif

                @if ($profile && ! $profile->onboarding_completed_at && $profile->onboarding_skipped_at)
                    <div class="kf-onboarding-reminder">
                        <div class="kf-onboarding-reminder-icon">✨</div>
                        <div class="kf-onboarding-reminder-body">
                            <p class="kf-onboarding-reminder-title">{{ __('Го прескокна поставувањето на профилот') }}</p>
                            <p class="kf-onboarding-reminder-desc">{{ __('Клиентите не можат да те најдат додека профилот не е завршен и верифициран. Ти треба само неколку минути.') }}</p>
                        </div>
                        <a href="{{ route('onboarding') }}" class="kf-btn" style="flex-shrink:0;text-decoration:none;">{{ __('Дополни го профилот') }}</a>
                    </div>
                @endif

                <div class="kf-stats">
                    <div class="kf-stat kf-accent">
                        <div class="kf-num">{{ $stats['pending_proposals'] }}</div>
                        <div class="kf-label">{{ __('Активни апликации') }}</div>
                    </div>
                    <div class="kf-stat kf-accent">
                        <div class="kf-num">{{ $stats['active_contracts'] }}</div>
                        <div class="kf-label">{{ __('Активни договори') }}</div>
                    </div>
                    <div class="kf-stat kf-verified">
                        <div class="kf-num">{{ $stats['completed_contracts'] }}</div>
                        <div class="kf-label">{{ __('Завршени проекти') }}</div>
                    </div>
                    <div class="kf-stat {{ $profile?->verified ? 'kf-verified' : 'kf-pending' }}">
                        <div class="kf-num kf-text">{{ $profile?->verified ? __('Верифициран ✓') : __('Чека верификација') }}</div>
                        <div class="kf-label">{{ __('Статус на профил') }}</div>
                    </div>
                </div>

                <div class="kf-section">
                    <div class="kf-section-title">
                        <h3>{{ __('Препорачани огласи за тебе') }}</h3>
                        <a href="{{ route('projects.browse') }}">{{ __('Погледни ги сите →') }}</a>
                    </div>

                    @if ($recommendedProjects->isEmpty())
                        <div class="kf-empty">
                            <span style="color:#14171F;font-weight:600;">{{ __('🚀 CreatorSpot штотуку почна во твојот регион. Твојот профил е меѓу првите — секој нов проект во твојата категорија прво стигнува до рани членови како тебе.') }}</span>
                            <div style="display:flex;justify-content:center;gap:10px;flex-wrap:wrap;margin-top:18px;">
                                @if ($profile && ! $profile->onboarding_completed_at)
                                    <a href="{{ route('onboarding') }}" class="kf-btn" style="text-decoration:none;">{{ __('Дополни го профилот') }}</a>
                                @endif
                                <a href="{{ route('projects.browse') }}" class="kf-clear">{{ __('Прегледај ги сите категории') }}</a>
                            </div>
                        </div>
                    @else
                        <div class="kf-grid">
                            @foreach ($recommendedProjects as $project)
                                <a href="{{ route('projects.show', $project) }}" class="kf-card">
                                    <div class="kf-cat-icon">{{ $project->categories->first()?->icon ?? '📁' }}</div>
                                    <p class="kf-card-title">{{ $project->title }}</p>
                                    <p class="kf-card-meta">{{ $project->categories->pluck('name')->join(', ') }}</p>
                                    <p class="kf-card-location">
                                        📍 {{ $project->remote_ok ? __('Remote') : ($project->city?->name ?? $project->country?->name ?? '—') }}
                                    </p>
                                    <p class="kf-card-desc">{{ $project->description }}</p>
                                    <div class="kf-posted-by">
                                        <x-avatar :user="$project->client" size="w-5 h-5" textSize="text-xs" />
                                        {{ $project->client->name }}
                                        <x-star-rating :rating="$project->client->reviews_received_count ? $project->client->reviews_received_avg_rating : null" :count="$project->client->reviews_received_count" size="text-xs" />
                                    </div>
                                    <div class="kf-card-foot">
                                        <span class="kf-budget">
                                            @if ($project->budget_min || $project->budget_max)
                                                {{ $project->budget_min ?? '?' }}–{{ $project->budget_max ?? '?' }} EUR
                                            @else
                                                {{ __('Цена по договор') }}
                                            @endif
                                        </span>
                                        <span class="kf-status kf-status-open">{{ __('Отворен') }}</span>
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
