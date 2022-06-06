<?php

declare(strict_types=1);

require_once 'fileUploader.php';

use Astrotomic\Translatable\Validation\RuleFactory;
use HexideDigital\HexideAdmin\Http\Middleware\LanguageMiddleware;
use Illuminate\Database\Eloquent\Model;


if (!function_exists('module_name_from_model')) {
    function module_name_from_model(Model $model): string
    {
        if (method_exists($model, 'getModuleName')) {
            return $model->getModuleName();
        }

        return $model->moduleName ?? $model->getTable();
    }
}

if (!function_exists('lang_rules')) {
    /**
     * @param array<string, string|array<string|string[]|\Illuminate\Validation\Rule>> $rules one-level attribute => $rules array of rules
     * @param array|null $locales
     *
     * @return array<string, string|array<string|string[]|\Illuminate\Validation\Rule>>
     */
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
     * @param array<string> $words массив склоняемого слова в трех вариантах (1, 2, багато)
     *
     * @return string
     */
    function declension_word(int $number, array $words): string
    {
        $indexes = [2, 0, 1, 1, 1, 2]; // індекси в масив слів

        $index = (4 < ($number % 100) && ($number % 100) < 20)
            ? 2                         // 5 - 19
            : $indexes[min($number % 10, 5)];   // *1 - *5

        return $words[$index];
    }
}

if (!function_exists('declension_key')) {
    /** Translation based declension function */
    function declension_key(int $number, string $key, ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        $words = trans('declensions.' . $key, [], $locale);

        if (is_string($words)) {
            $words = explode('|', $words);
        }

        return declension_word($number, $words);
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

        if (URL::isValidUrl($path)) {
            return $path;
        }

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
        if (empty($path)) {
            return $path;
        }

        return str_replace([
            config('app.url') . '/',
            'storage/',
            '/storage',
            '/storage/',
        ], '', $path);
    }
}
