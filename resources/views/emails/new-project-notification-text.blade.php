{{ __('Нов оглас во твоите категории') }}

{{ $project->title }}

@php
    $budgetLine = ($project->budget_min || $project->budget_max)
        ? ($project->budget_min ?? '?').'–'.($project->budget_max ?? '?').' EUR'
        : __('Цена по договор');
@endphp
{{ __('Категорија') }}: {{ $project->categories->pluck('name')->join(', ') }}
{{ __('Буџет') }}: {{ $budgetLine }}
{{ __('Локација') }}: {{ $project->remote_ok ? __('Remote') : ($project->city?->name ?? $project->country?->name ?? '—') }}

{{ __('Погледни оглас →') }} {{ route('projects.show', $project) }}
