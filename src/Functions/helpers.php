<?php


if (!function_exists('lang_rules')) {
    /**
     * options: prefix: string, old_new: bool, lang_key string, locales: array
     *
     * @param array $rules
     * @param array $options
     * @return array
     */
    function lang_rules(array $rules, array $options = []): array
    {
        return \HexideDigital\HexideAdmin\Classes\LanguageRequestRules::getLangRules($rules, $options);
    }
}

use App\Http\Middleware\LanguageMiddleware;
if (!function_exists('locale_prefix')) {
    /**
     * @return string
     */
    function locale_prefix(): string
    {
        $locale_prefix = LanguageMiddleware::getLocale();
        if (!empty($locale_prefix) || $locale_prefix === config('app.locale')) {
            $locale_prefix = '';
        }
        return '/' . $locale_prefix;
    }
}
