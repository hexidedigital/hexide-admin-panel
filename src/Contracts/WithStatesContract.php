<?php

namespace HexideDigital\HexideAdmin\Contracts;

/**
 *
 */
interface WithStatesContract
{

    /**
     * @return array
     */
    public static function getStates(): array;

    /**
     * @return array
     */
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
