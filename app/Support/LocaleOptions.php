<?php

namespace App\Support;

class LocaleOptions
{
    /**
     * Switcher entries. Two entries can point at the same locale (the two
     * Albanian flag variants both resolve to 'sq') — the "option" key is
     * only used to remember which flag to display, never for translation
     * lookups, which always use the underlying 'locale' value.
     */
    public const OPTIONS = [
        'mk' => ['locale' => 'mk', 'flag' => '🇲🇰', 'label' => 'Македонски'],
        'en' => ['locale' => 'en', 'flag' => '🇬🇧', 'label' => 'English'],
        'sr' => ['locale' => 'sr', 'flag' => '🇷🇸', 'label' => 'Српски'],
        'hr' => ['locale' => 'hr', 'flag' => '🇭🇷', 'label' => 'Hrvatski'],
        'sq_al' => ['locale' => 'sq', 'flag' => '🇦🇱', 'label' => 'Shqip (Shqipëri)'],
        'sq_xk' => ['locale' => 'sq', 'flag' => '🇽🇰', 'label' => 'Shqip (Kosovë)'],
        'bg' => ['locale' => 'bg', 'flag' => '🇧🇬', 'label' => 'Български'],
        'el' => ['locale' => 'el', 'flag' => '🇬🇷', 'label' => 'Ελληνικά'],
    ];

    /**
     * Work out which option key is "selected" given the active locale and
     * an optional remembered option (from the 'locale_option' cookie).
     */
    public static function currentOptionKey(string $locale, ?string $rememberedOption): string
    {
        if ($rememberedOption
            && isset(self::OPTIONS[$rememberedOption])
            && self::OPTIONS[$rememberedOption]['locale'] === $locale) {
            return $rememberedOption;
        }

        foreach (self::OPTIONS as $key => $option) {
            if ($option['locale'] === $locale) {
                return $key;
            }
        }

        return 'mk';
    }

    /**
     * OPTIONS reordered so the visitor's current/detected option comes
     * first, followed by the rest sorted alphabetically by locale code.
     *
     * @return array<string, array{locale: string, flag: string, label: string}>
     */
    public static function orderedOptions(string $currentOption): array
    {
        if (! isset(self::OPTIONS[$currentOption])) {
            $currentOption = 'mk';
        }

        $rest = self::OPTIONS;
        unset($rest[$currentOption]);

        uasort($rest, fn (array $a, array $b) => $a['locale'] <=> $b['locale']);

        return [$currentOption => self::OPTIONS[$currentOption]] + $rest;
    }
}
