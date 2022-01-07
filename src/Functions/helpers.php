<?php

require_once 'fileUploader.php';

use Astrotomic\Translatable\Validation\RuleFactory;
use HexideDigital\HexideAdmin\Http\Middleware\LanguageMiddleware;

if (!function_exists('lang_rules')) {
    function lang_rules(array $rules, array $locales = null): array
    {
        return RuleFactory::make($rules, null, null, null, $locales);
    }
}

if (!function_exists('locale_prefix')) {
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
        $ar = array(2, 0, 1, 1, 1, 2); // індекси в масив слів

        return $words[(4 < $number % 100 && $number % 100 < 20)
            ? 2                         // 5 - 19
            : $ar[min($number % 10, 5)]   // *1-*5
        ];
    }
}


if (!function_exists('thumb_adapt')) {
    /**
     * Create image thumbnail for given dimension
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\UrlGenerator|string
     */
    function thumb_adapt(string $path, int $dim)
    {
        $thumb = null;

        $path = clear_filepath($path);

        if (URL::isValidUrl($path)) return $path;

        $path = 'storage/' . $path;

        if (File::exists($path)) {
            $img_info = getimagesize($path);

            if (!empty($img_info)) {
                list($width, $height) = $img_info;

                if ($width > $height) {
                    $thumb = Thumb::thumb_adapt($path, $dim, null);
                } else {
                    $thumb = Thumb::thumb_adapt($path, null, $dim);
                }

                $thumb = url($thumb->link());
            }
        }

        return $thumb ?: asset("/img/800x800.png");
    }
}

if (!function_exists('clear_filepath')) {
    /**
     * Remove current domain url, storage path
     * If path is external url, will be returned same url
     */
    function clear_filepath(?string $path): string
    {
        if (empty($path)) return $path;

        return str_replace([
            config('app.url') . '/',
            'storage/',
            '/storage',
            '/storage/',
        ], '', $path);
    }
}
