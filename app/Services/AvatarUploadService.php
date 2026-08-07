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

        // Read raw bytes through the file's own accessor rather than handing
        // Intervention Image the file object directly: Livewire's
        // TemporaryUploadedFile resolves getRealPath()/getPathname() against
        // whatever disk its temp upload lives on, which isn't a real local
        // path when that disk is S3-backed, and Intervention's decoder
        // assumes a local filesystem path in that case.
        $contents = method_exists($file, 'get') ? $file->get() : file_get_contents($file->getRealPath());

        $encoded = (new ImageManager(new Driver))
            ->decode($contents)
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
