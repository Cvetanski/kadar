@php
    $statusLabels = [
        'pending' => __('Чека одговор'),
        'accepted' => __('Прифатена'),
        'rejected' => __('Одбиена'),
        'withdrawn' => __('Повлечена'),
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Мои апликации') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('partials.creator-fancy-styles')
            <div class="kf-wrap">

                @if ($proposals->isEmpty())
                    <div class="kf-empty">
                        {{ __('Сѐ уште немаш поднесено ниту една апликација.') }}
                        <a href="{{ route('projects.browse') }}" style="color:#0B6FE0;font-weight:700;">{{ __('Разгледај отворени огласи →') }}</a>
                    </div>
                @else
                    <div class="kf-grid">
                        @foreach ($proposals as $proposal)
                            <a href="{{ route('projects.show', $proposal->project) }}" class="kf-card">
                                <div class="kf-cat-icon">{{ $proposal->project->categories->first()?->icon ?? '📁' }}</div>
                                <p class="kf-card-title">{{ $proposal->project->title }}</p>
                                <p class="kf-card-meta">{{ __('Твоја цена:') }} {{ $proposal->price }} EUR</p>
                                <div class="kf-card-foot" style="border-top:none;padding-top:0;">
                                    <span class="kf-status kf-status-{{ $proposal->status }}">
                                        {{ $statusLabels[$proposal->status] }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="kf-pagination">
                        {{ $proposals->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
