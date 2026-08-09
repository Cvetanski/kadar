@props([
    'videoId' => null,
    'embedSrc' => null,
    'thumbnailUrl' => null,
    'title' => null,
])

@php
    $title ??= __('Демо видео');
    // video-id is a YouTube convenience: when given, it derives the embed
    // src and thumbnail so callers don't have to build YouTube URLs by
    // hand. Other providers (e.g. Vimeo) pass embed-src/thumbnail-url
    // directly instead — same lite-embed markup either way.
    $embedSrc ??= $videoId ? "https://www.youtube.com/embed/{$videoId}?autoplay=1" : null;
    $thumbnailUrl ??= $videoId ? "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg" : null;
    $thumbnailFallback = $videoId ? "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg" : null;
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
            @if ($thumbnailUrl)
                <img
                    src="{{ $thumbnailUrl }}"
                    @if ($thumbnailFallback) onerror="this.onerror=null;this.src='{{ $thumbnailFallback }}';" @endif
                    alt="{{ $title }}"
                    loading="lazy"
                    class="absolute inset-0 h-full w-full object-cover"
                >
            @else
                <div class="absolute inset-0" style="background: linear-gradient(135deg, #2a2f3a, #14171F);"></div>
            @endif
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
            src="{{ $embedSrc }}"
            title="{{ $title }}"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen
        ></iframe>
    </template>
</div>
