<?php

namespace HexideDigital\HexideAdmin\Contracts;

interface WithTypesContract
{

    public static function getTypes(): array;

    public static function getTypesKeys(): array;

    /**
     * @param int|string|null $type
     * @return int|string|null
     */
    public static function getValueByTypeKey($type);

}
