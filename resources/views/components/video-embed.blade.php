@props([
    'videoId',
    'title' => null,
])

@php
    $title ??= __('Демо видео');
@endphp

<div
    x-data="{ playing: false }"
    class="relative w-full overflow-hidden rounded-xl bg-black"
    style="aspect-ratio: 16 / 9;"
>
    <template x-if="! playing">
        <button
            type="button"
            @click="playing = true"
            class="group absolute inset-0 flex items-center justify-center"
            aria-label="{{ __('Пушти видео') }}"
        >
            <img
                src="https://img.youtube.com/vi/{{ $videoId }}/maxresdefault.jpg"
                onerror="this.onerror=null;this.src='https://img.youtube.com/vi/{{ $videoId }}/hqdefault.jpg';"
                alt="{{ $title }}"
                loading="lazy"
                class="absolute inset-0 h-full w-full object-cover"
            >
            <span class="absolute inset-0 bg-black/20 transition group-hover:bg-black/30"></span>
            <span
                class="relative flex h-16 w-16 sm:h-20 sm:w-20 items-center justify-center rounded-full shadow-lg transition group-hover:scale-105"
                style="background: linear-gradient(135deg, rgba(45,130,232,0.9), rgba(9,88,181,0.9));"
            >
                <svg class="ms-1 h-7 w-7 sm:h-8 sm:w-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
            </span>
        </button>
    </template>

    <template x-if="playing">
        <iframe
            class="absolute inset-0 h-full w-full"
            src="https://www.youtube.com/embed/{{ $videoId }}?autoplay=1"
            title="{{ $title }}"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen
        ></iframe>
    </template>
</div>
