@props(['item'])

@php
    $source = \App\Support\PortfolioSource::type($item->media_url);
    $youtubeId = $source === 'youtube' ? \App\Support\PortfolioSource::youtubeId($item->media_url) : null;
    $vimeoId = $source === 'vimeo' ? \App\Support\PortfolioSource::vimeoId($item->media_url) : null;
@endphp

@if ($youtubeId)
    <x-video-embed :video-id="$youtubeId" :title="$item->title" />
@elseif ($vimeoId)
    <x-video-embed
        :embed-src="'https://player.vimeo.com/video/'.$vimeoId.'?autoplay=1'"
        :thumbnail-url="$item->thumbnail_url"
        :title="$item->title"
    />
@elseif ($source === 'instagram')
    <x-portfolio-instagram-embed :url="$item->media_url" :title="$item->title" />
@elseif ($source === 'facebook')
    <x-portfolio-facebook-embed :url="$item->media_url" :video="\App\Support\PortfolioSource::isFacebookVideo($item->media_url)" :title="$item->title" />
@else
    <a
        href="{{ $item->media_url }}"
        target="_blank"
        rel="noopener"
        class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-700"
    >
        🌐 {{ __('Посети сајт') }} →
    </a>
@endif
