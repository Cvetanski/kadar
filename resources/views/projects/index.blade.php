@php
    $statusLabels = [
        'open' => __('Отворен'),
        'in_progress' => __('Во тек'),
        'completed' => __('Завршен'),
        'cancelled' => __('Откажан'),
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Мои Огласи') }}</h2>
            <x-primary-button onclick="window.location='{{ route('projects.create') }}'">{{ __('Креирај Оглас') }}</x-primary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('partials.creator-fancy-styles')
            <div class="kf-wrap">
                @if ($projects->isEmpty())
                    <div class="kf-empty">
                        {{ __('Сѐ уште немаш објавено проект.') }}
                        <a href="{{ route('projects.create') }}" style="color:#0B6FE0;font-weight:700;">{{ __('Објави нов оглас →') }}</a>
                    </div>
                @else
                    <div class="kf-grid">
                        @foreach ($projects as $project)
                            <a href="{{ route('projects.show', $project) }}" class="kf-card">
                                <div class="kf-cat-icon">{{ $project->categories->first()?->icon ?? '📁' }}</div>
                                <p class="kf-card-title">{{ $project->title }}</p>
                                <p class="kf-card-meta">{{ $project->categories->pluck('name')->join(', ') }} · {{ $project->proposals_count }} {{ __('понуди') }}</p>
                                <div class="kf-card-foot" style="border-top:none;padding-top:0;">
                                    <span class="kf-status kf-status-{{ $project->status }}">
                                        {{ $statusLabels[$project->status] }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="kf-pagination">
                        {{ $projects->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
