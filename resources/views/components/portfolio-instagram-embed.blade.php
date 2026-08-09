@props([
    'url',
    'title' => null,
])

{{--
    Instagram's public embed widget (blockquote + embed.js) needs no API
    credentials — it's the same thing every blog uses. Left click-to-load
    (lite embed) so we don't pull in Instagram's script/iframe for every
    portfolio item on page load, only when a visitor actually wants it.
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
            aria-label="{{ __('Погледни на Instagram') }}"
        >
            <span
                class="flex h-14 w-14 items-center justify-center rounded-2xl shadow-sm transition group-hover:scale-105"
                style="background: linear-gradient(135deg,#F58529,#DD2A7B,#8134AF);"
            >
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                </svg>
            </span>
            <span class="text-sm font-semibold text-gray-700">{{ __('Погледни на Instagram') }}</span>
        </button>
    </template>

    <template x-if="loaded">
        <div
            class="w-full"
            x-init="$nextTick(() => {
                if (window.instgrm) {
                    window.instgrm.Embeds.process();
                } else {
                    let s = document.createElement('script');
                    s.src = 'https://www.instagram.com/embed.js';
                    s.async = true;
                    document.body.appendChild(s);
                }
            })"
        >
            <blockquote class="instagram-media" data-instgrm-permalink="{{ $url }}" data-instgrm-version="14" style="width:100%;margin:0;"></blockquote>
        </div>
    </template>
</div>
