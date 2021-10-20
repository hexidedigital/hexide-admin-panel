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
    public function getTypes(): array;

    /**
     * @return array
     */
    public function getTypesKeys(): array;

    /**
     * @param int|string|null $type
     * @return int|string|null
     */
    public function getValueByKey($type);

}
