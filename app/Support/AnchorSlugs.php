<?php

namespace App\Support;

class AnchorSlugs
{
    /**
     * Locale-specific anchor slugs for the landing page's in-page sections,
     * so the URL fragment (#...) matches the visitor's selected language
     * instead of always being Macedonian.
     */
    private const SLUGS = [
        'kategorii' => [
            'mk' => 'kategorii', 'en' => 'categories', 'sr' => 'kategorije', 'hr' => 'kategorije',
            'sq' => 'kategorite', 'bg' => 'kategorii', 'el' => 'katigories',
        ],
        'kreativci' => [
            'mk' => 'kreativci', 'en' => 'creatives', 'sr' => 'kreativci', 'hr' => 'kreativci',
            'sq' => 'krijuesit', 'bg' => 'tvortsi', 'el' => 'dimiourgi',
        ],
        'kako' => [
            'mk' => 'kako', 'en' => 'how-it-works', 'sr' => 'kako-funkcionise', 'hr' => 'kako-funkcionira',
            'sq' => 'si-funksionon', 'bg' => 'kak-raboti', 'el' => 'pos-leitourgei',
        ],
    ];

    public static function for(string $key): string
    {
        return self::SLUGS[$key][app()->getLocale()] ?? self::SLUGS[$key]['mk'];
    }
}
