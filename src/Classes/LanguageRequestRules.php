<?php

namespace HexideDigital\HexideAdmin\Classes;

use Arr;

Class LanguageRequestRules
{

    /**
     * options: prefix: string, old_new: bool, lang_key string, locales: array
     *
     * @param array $rules
     * @param array $options
     * @return array
     */
    public static function getLangRules(array $rules, array $options = []): array
    {
        $prefix = Arr::get($options, 'prefix', '');
        $old_new = Arr::get($options, 'old_new', false);
        $lang_key = Arr::get($options, 'lang_key', 'lang_rules');
        $locales = Arr::get($options, 'locales', config('app.locales'));


        foreach ($rules as $attribute => $rule) {
            if (is_string($rule) || (is_array($rule) && !Arr::isAssoc($rule))) {
                if ($old_new) {
                    $rules[self::prefix($prefix, 'new') .  $attribute] = $rule;
                    $rules[self::prefix($prefix, 'old') .  $attribute] = $rule;
                } else {
                    $rules[self::prefix($prefix) .  $attribute] = $rule;
                }
            }
        }

        if(!empty($rules[$lang_key])) {
            foreach ($locales as $locale) {
                if ($old_new) {
                    foreach ($rules[$lang_key] as $attribute => $rule) {
                        $rules[self::prefix($prefix, 'new') . $locale . '.' . $attribute] = $rule;
                        $rules[self::prefix($prefix, 'old') . $locale . '.' . $attribute] = $rule;
                    }
                } else {
                    foreach ($rules[$lang_key] as $attribute => $rule) {
                        $rules[self::prefix($prefix) . $locale . '.'. $attribute] = $rule;
                    }
                }
            }
            unset($rules[$lang_key]);
        }

        return $rules;
    }

    private static function prefix(?string $prefix = '', ?string $append = ''): ?string
    {
        if (empty($prefix)) return null;

        $prefix = trim($prefix, '.');

        if (!empty($append)) {
            $prefix = implode('.', [$prefix, $append, '*']);
        }

        return $prefix . '.';
    }
}
