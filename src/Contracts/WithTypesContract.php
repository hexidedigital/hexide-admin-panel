<?php

namespace HexideDigital\HexideAdmin\Contracts;

/**
 *
 */
interface WithTypesContract
{

    /**
     * @return array
     */
    public static function getTypes(): array;

    /**
     * @return array
     */
    public static function getTypesKeys(): array;

    /**
     * @param int|string|null $type
     * @return int|string|null
     */
    public static function getValueByKey($type);

}
