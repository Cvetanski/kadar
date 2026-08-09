@props([
    'url',
    'video' => false,
    'title' => null,
])

@php
    $plugin = $video ? 'video.php' : 'post.php';
    $embedSrc = 'https://www.facebook.com/plugins/'.$plugin.'?href='.urlencode($url).'&show_text=false';
@endphp

{{--
    Facebook's Social Plugins (video.php / post.php) are plain iframes —
    no API key or App Review needed, unlike the Graph API oEmbed endpoint.
    Click-to-load like the other embeds, so the iframe only loads on demand.
--}}
<div
    x-data="{ loaded: false }"
    class="relative w-full overflow-hidden rounded-xl border border-gray-200 bg-white"
    style="min-height: 340px;"
>
    <template x-if="! loaded">
        <button
            type="button"
            @click="loaded = true"
            class="group flex w-full flex-col items-center justify-center gap-3"
            style="min-height: 340px;"
            aria-label="{{ __('Погледни на Facebook') }}"
        >
            <span class="flex h-14 w-14 items-center justify-center rounded-2xl shadow-sm transition group-hover:scale-105" style="background:#1877F2;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="#fff">
                    <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                </svg>
            </span>
            <span class="text-sm font-semibold text-gray-700">{{ __('Погледни на Facebook') }}</span>
        </button>
    </template>

    <template x-if="loaded">
        <iframe
            src="{{ $embedSrc }}"
            title="{{ $title }}"
            style="width:100%;min-height:340px;border:none;overflow:hidden;"
            scrolling="no"
            frameborder="0"
            allowfullscreen="true"
            allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"
        ></iframe>
    </template>
</div>
