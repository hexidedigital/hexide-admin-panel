<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin;

if (!function_exists('input_to_dot_name')) {
    /** Transform input-case to dot-case */
    function input_to_dot_name(?string $fieldName): string
    {
        if (empty($fieldName)) {
            return $fieldName;
        }

        // some[path][to][data]
        // some.path.to.data

        $map = [
            '.' => '_',
            '[]' => '',
            '[' => '.',
            ']' => '',
        ];

        return str_replace(array_keys($map), array_values($map), $fieldName);
    }
}


if (!function_exists('class_uses_contains')) {
    /**
     * @param object|string $class
     * @param string $trail
     * @param bool $recursive
     * @return bool
     */
    function class_uses_contains($class, string $trail, bool $recursive = true): bool
    {
        $uses = $recursive ? class_uses_recursive($class) : class_uses($class);

        return in_array($trail, array_keys($uses));
    }
}

if (!function_exists('resolve_translation')) {
    function resolve_translation(string $scope, string $original, ...$options)
    {
        $key = $scope . '.' . $original;
        $translation = __($key, ...$options);
        if ($key === $translation) {
            return $original;
        }

        return $translation;
    }
}

if (!function_exists('__attr')) {
    function __attr(string $original, ...$options)
    {
        return resolve_translation('admin_labels.attributes', $original, $options);
    }
}
