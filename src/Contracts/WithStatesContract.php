<?php

namespace HexideDigital\HexideAdmin\Contracts;

interface WithStatesContract
{
    public static function getStates(): array;

    public static function getStatesKeys(): array;

    /**
     * @param int|string|null $type
     * @return int|string|null
     */
    public static function getValueByKey($type);

    /**
     * @param int|string|null $value
     * @return int|string|null
     */
    public static function getKeyByValue($value);
}
