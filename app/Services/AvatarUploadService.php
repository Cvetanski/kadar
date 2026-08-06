<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;

class AvatarUploadService
{
    public function store(UploadedFile $file): string
    {
        $path = 'avatars/'.Str::uuid().'.jpg';

        $encoded = (new ImageManager(new Driver))
            ->decode($file)
            ->cover(400, 400)
            ->encode(new JpegEncoder(quality: 85));

        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    /**
     * Download a remote image (e.g. a Google account picture) and store it
     * locally like a normal upload, so it doesn't depend on the source's
     * CDN being reachable every time the avatar is displayed. Returns null
     * on any failure so the caller can fall back to the raw URL.
     */
    public function storeFromUrl(string $url): ?string
    {
        try {
            $response = Http::timeout(5)->get($url);
        } catch (\Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $path = 'avatars/'.Str::uuid().'.jpg';

        try {
            $encoded = (new ImageManager(new Driver))
                ->decode($response->body())
                ->cover(400, 400)
                ->encode(new JpegEncoder(quality: 85));

            Storage::disk('public')->put($path, (string) $encoded);
        } catch (\Throwable) {
            return null;
        }

        return $path;
    }
}
