<?php

namespace App\Support;

class PortfolioSource
{
    /**
     * Detect which embeddable platform a portfolio link belongs to, purely
     * from the URL's host — used to pick the right embed rendering, without
     * needing the media_type the creator picked when adding the item.
     */
    public static function type(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST) ?? '';
        $host = strtolower(preg_replace('/^www\./', '', $host));

        return match (true) {
            in_array($host, ['youtube.com', 'youtu.be', 'm.youtube.com'], true) => 'youtube',
            in_array($host, ['vimeo.com', 'player.vimeo.com'], true) => 'vimeo',
            in_array($host, ['instagram.com', 'instagr.am'], true) => 'instagram',
            in_array($host, ['facebook.com', 'm.facebook.com', 'fb.watch'], true) => 'facebook',
            default => 'website',
        };
    }

    public static function youtubeId(string $url): ?string
    {
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([a-zA-Z0-9_-]{6,})/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public static function vimeoId(string $url): ?string
    {
        if (preg_match('/vimeo\.com\/(?:.*\/)?(\d+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Facebook has separate Social Plugins for videos vs. posts — pick the
     * video one when the link clearly points at a video, otherwise fall
     * back to the general post plugin (which also handles reels/photos).
     */
    public static function isFacebookVideo(string $url): bool
    {
        return str_contains($url, '/videos/') || str_contains($url, '/watch/') || str_contains($url, 'fb.watch');
    }
}
