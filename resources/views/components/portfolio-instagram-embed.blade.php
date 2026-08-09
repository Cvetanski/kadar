@props([
    'url',
    'title' => null,
])

{{--
    Instagram's public embed widget (blockquote + embed.js) needs no API
    credentials — it's the same thing every blog uses. Rendered immediately
    (no click-to-load button) so visitors see the real post preview with
    its own thumbnail straight away, instead of a generic branded icon.
--}}
<div
    class="relative w-full overflow-hidden rounded-xl border border-gray-200 bg-white"
    style="min-height: 340px;"
    x-data
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
