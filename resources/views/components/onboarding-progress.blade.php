@props(['step', 'totalSteps'])

@php
    $labels = [
        1 => __('Категорија'),
        2 => __('Вештини'),
        3 => __('Локација'),
        4 => __('Профил'),
        5 => __('Портфолио'),
        6 => __('Социјални мрежи'),
        7 => __('Преглед'),
    ];
@endphp

<div class="flex flex-wrap gap-2 text-sm">
    @foreach ($labels as $num => $label)
        <div @class([
            'flex items-center gap-2 px-3 py-1 rounded-full',
            'bg-indigo-600 text-white' => $num === $step,
            'bg-indigo-100 text-indigo-700' => $num < $step,
            'bg-gray-100 text-gray-400' => $num > $step,
        ])>
            <span class="font-semibold">{{ $num }}</span>
            <span>{{ $label }}</span>
        </div>
    @endforeach
</div>
