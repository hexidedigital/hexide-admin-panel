<?php

namespace HexideDigital\HexideAdmin\Classes;

use Arr;
use Astrotomic\Translatable\Validation\RuleFactory;

class LanguageRequestRules
{

    private static string $prefix = '{{';
    private static string $suffix = '}}';

    /**
     * options: prefix: string, old_new: bool, lang_key string, locales: array
     *
     * @param array $_rules
     * @param array $options
     * @return array
     */
    public static function getLangRules(array $_rules, array $options = []): array
    {
        $prefix = Arr::get($options, 'prefix', '');
        $old_new = Arr::get($options, 'old_new', false);
        $lang_key = Arr::get($options, 'lang_key', 'lang_rules');
        $locales = Arr::get($options, 'locales', config('translatable.locale'));

        $result_rules = [];
        $rules = [];

        foreach ($_rules as $attribute => $rule) {

            if (is_string($rule) || (is_array($rule) && !Arr::isAssoc($rule))) {
                if ($old_new) {
                    $rules[self::prefix($prefix) . 'new.' . $attribute] = $rule;
                    $rules[self::prefix($prefix) . 'old.' . $attribute] = $rule;
                } else {
                    $rules[self::prefix($prefix) . self::cl($attribute)] = $rule;
                }
            }
        }
        $result_rules = RuleFactory::make(
            $rules,
            \Astrotomic\Translatable\Validation\RuleFactory::FORMAT_ARRAY,
            static::$prefix,
            static::$suffix,
            $locales
        );

        if (!empty($_rules[$lang_key])) {
            $rules = [];
            $lang_rules = $_rules[$lang_key];

            if ($old_new) {
                foreach ($lang_rules as $attribute => $rule) {
                    $rules[self::prefix($prefix) . 'new.' . static::translated($attribute)] = $rule;
                    $rules[self::prefix($prefix) . 'old.' . static::translated($attribute)] = $rule;
                }
            } else {
                foreach ($lang_rules as $attribute => $rule) {
                    $rules[self::prefix($prefix) . static::translated($attribute)] = $rule;
                }
            }

            $result_rules += RuleFactory::make(
                $rules,
                \Astrotomic\Translatable\Validation\RuleFactory::FORMAT_ARRAY,
                static::$prefix,
                static::$suffix,
                $locales
            );
        }

        return $result_rules;
    }

    /**
     * Clear string from dots
     *
     * @param string|null $value
     * @return string
     */
    private static function cl(?string $value): string
    {
        return trim($value, '.');
    }

    private static function prefix($prefix): string
    {
        $prefix = self::cl($prefix);
        return empty($prefix) ? '' : $prefix . '.';
    }

    private static function translated(string $attribute): string
    {
        return static::$prefix . static::cl($attribute) . static::$suffix;
    }
}
