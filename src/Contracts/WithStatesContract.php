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
    public function getStates(): array;

    /**
     * @return array
     */
    public function getStatesKeys(): array;

    /**
     * @param int|string|null $type
     * @return int|string|null
     */
    public function getValueByKey($type);

    /**
     * @param int|string|null $value
     * @return int|string|null
     */
    public function getKeyByValue($value);

}
