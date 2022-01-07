<?php

namespace HexideDigital\HexideAdmin\Facades;

use Illuminate\Support\Facades\Facade;

class Thumb extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'thumb';
    }
}
