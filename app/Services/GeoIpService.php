<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeoIpService
{
    /**
     * Best-effort guess at the visitor's country, for use as a form prefill
     * only. Returns the ISO 3166-1 alpha-2 country code, or null if the
     * lookup fails or the IP can't be resolved (private/reserved IPs,
     * rate limiting, timeouts) — callers must treat null as "no guess" and
     * never block on it.
     */
    public function guessCountryCode(string $ip): ?string
    {
        if (! $this->isPubliclyRoutable($ip)) {
            return null;
        }

        return Cache::remember("geoip:{$ip}", now()->addDay(), function () use ($ip) {
            try {
                $response = Http::timeout(2)
                    ->connectTimeout(2)
                    ->get("http://ip-api.com/json/{$ip}", ['fields' => 'status,countryCode']);

                if ($response->successful() && $response->json('status') === 'success') {
                    return $response->json('countryCode');
                }
            } catch (Throwable $e) {
                Log::debug('GeoIP lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);
            }

            return null;
        });
    }

    private function isPubliclyRoutable(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }
}
