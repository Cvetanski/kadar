<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const SUPPORTED_LOCALES = ['mk', 'sr', 'hr', 'sq', 'bg', 'el', 'en'];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale($this->resolveLocale($request));

        return $next($request);
    }

    /**
     * Country (ISO 3166-1 alpha-2, from Cloudflare's CF-IPCountry header) to
     * app locale. Countries not listed here fall through to the
     * Accept-Language guess, then the app default.
     */
    private const COUNTRY_LOCALES = [
        'MK' => 'mk',
        'RS' => 'sr',
        'ME' => 'sr',
        'BA' => 'sr',
        'HR' => 'hr',
        'AL' => 'sq',
        'XK' => 'sq',
        'BG' => 'bg',
        'GR' => 'el',
        'CY' => 'el',
    ];

    /**
     * Priority: an authenticated user's saved preference, then the guest
     * locale cookie, then the visitor's country (from Cloudflare's
     * CF-IPCountry header — more reliable than browser language for
     * Balkan visitors whose OS/browser is set to English), then a
     * best-effort guess from the Accept-Language header, then the app
     * default.
     */
    private function resolveLocale(Request $request): string
    {
        $user = $request->user();

        if ($user && in_array($user->locale, self::SUPPORTED_LOCALES, true)) {
            return $user->locale;
        }

        $cookie = $request->cookie('locale');

        if (is_string($cookie) && in_array($cookie, self::SUPPORTED_LOCALES, true)) {
            return $cookie;
        }

        return $this->detectFromCountry($request)
            ?? $this->detectFromAcceptLanguage($request)
            ?? config('app.locale');
    }

    private function detectFromCountry(Request $request): ?string
    {
        $country = $request->headers->get('CF-IPCountry');

        if (! is_string($country)) {
            return null;
        }

        return self::COUNTRY_LOCALES[strtoupper($country)] ?? null;
    }

    private function detectFromAcceptLanguage(Request $request): ?string
    {
        $header = $request->headers->get('Accept-Language');

        if (! is_string($header) || $header === '') {
            return null;
        }

        $entries = [];

        foreach (explode(',', $header) as $part) {
            $part = trim($part);

            if ($part === '') {
                continue;
            }

            [$tag, $weight] = array_pad(explode(';q=', $part), 2, '1.0');

            $entries[] = ['tag' => strtolower(trim($tag)), 'q' => (float) $weight];
        }

        usort($entries, fn (array $a, array $b) => $b['q'] <=> $a['q']);

        foreach ($entries as $entry) {
            $primary = explode('-', $entry['tag'])[0];

            if (in_array($primary, self::SUPPORTED_LOCALES, true)) {
                return $primary;
            }
        }

        return null;
    }
}
