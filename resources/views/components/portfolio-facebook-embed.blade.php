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
    Rendered immediately (no click-to-load button) so visitors see the real
    post/video preview straight away; native `loading="lazy"` keeps it from
    actually fetching until the iframe scrolls into view.
--}}
<div class="relative w-full overflow-hidden rounded-xl border border-gray-200 bg-white" style="min-height: 340px;">
    <iframe
        src="{{ $embedSrc }}"
        title="{{ $title }}"
        loading="lazy"
        style="width:100%;min-height:340px;border:none;overflow:hidden;"
        scrolling="no"
        frameborder="0"
        allowfullscreen="true"
        allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"
    ></iframe>
</div>
