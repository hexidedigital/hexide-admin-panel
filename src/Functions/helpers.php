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

if (!function_exists('wrap_rules')) {
    /**
     * @param string $wrap
     * @param array $rules
     * @return array
     */
    function wrap_rules(string $wrap, array $rules): array
    {
        $arr = [];

        foreach ($rules as $attr => $rule) {
            $arr[$wrap . '.' . $attr] = $rule;
        }

        return $arr;
    }
}

use HexideDigital\HexideAdmin\Http\Middleware\LanguageMiddleware;
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
