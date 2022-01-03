<?php

use Astrotomic\Translatable\Validation\RuleFactory;
use HexideDigital\HexideAdmin\Http\Middleware\LanguageMiddleware;

if (!function_exists('lang_rules')) {
    /**
     * @param array $rules
     * @param array|null $locales
     * @return array
     */
    function lang_rules(array $rules, array $locales = null): array
    {
        return RuleFactory::make($rules, null, null, null, $locales);
    }
}

if (!function_exists('locale_prefix')) {
    /**
     * @return string
     */
    function locale_prefix(): string
    {
        $locale_prefix = LanguageMiddleware::getLocaleFromUrl();
        if (!empty($locale_prefix) || $locale_prefix === config('app.locale')) {
            $locale_prefix = '';
        }
        return '/' . $locale_prefix;
    }
}

if (!function_exists('declension_word')) {
    /**
     * <p>приклад                  0           1           2                </p>
     * <p>declension_word(21, ['елемент', 'елемента', 'елементів']) = елемент</p>
     * <p>declension_word(24, ['елемент', 'елемента', 'елементів']) = елементи</p>
     * <p>declension_word(36, ['елемент', 'елемента', 'елементів']) = елементів</p>
     *
     * @param int $number число, от которого будет зависеть форма нужного слова;
     * @param array $words массив склоняемого слова в трех вариантах (1, 2, багато)
     * @return string
     */
    function declension_word(int $number, array $words): string
    {
        $ar= array (2, 0, 1, 1, 1, 2); // індекси в масив слів

        return $words[(4 < $number%100 && $number%100 < 20)
            ? 2                         // 5 - 19
            : $ar[min($number%10, 5)]   // *1-*5
        ];
    }
}
