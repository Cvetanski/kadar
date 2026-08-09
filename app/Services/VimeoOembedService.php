<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class VimeoOembedService
{
    /**
     * Vimeo's oEmbed endpoint is public and needs no credentials, but unlike
     * YouTube it has no predictable thumbnail URL pattern — this is the only
     * way to get one. Best-effort: any failure just means no thumbnail.
     */
    public function thumbnailFor(string $url): ?string
    {
        try {
            $response = Http::timeout(4)->get('https://vimeo.com/api/oembed.json', ['url' => $url]);
        } catch (\Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        return $response->json('thumbnail_url');
    }
}
