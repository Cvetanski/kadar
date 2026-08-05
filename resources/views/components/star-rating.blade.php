@props(['rating' => null, 'count' => 0, 'size' => 'text-sm', 'label' => null])

@if ($rating)
    <div class="inline-flex items-center gap-1.5 {{ $size }}">
        <div class="relative inline-block leading-none">
            <span class="text-gray-300">★★★★★</span>
            <span class="absolute inset-0 overflow-hidden text-amber-400"
                style="width: {{ min(100, max(0, round($rating / 5 * 100))) }}%">★★★★★</span>
        </div>
        <span class="font-semibold text-gray-700">{{ number_format($rating, 1) }}</span>
        <span class="text-gray-400">({{ $count }})</span>
    </div>
@else
    <span class="inline-flex items-center gap-1 {{ $size }} font-semibold text-gray-400">
        <span>✦</span> {{ $label ?? __('Нов') }}
    </span>
@endif
