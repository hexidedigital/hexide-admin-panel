<?php

namespace HexideDigital\HexideAdmin\Facades;

use Illuminate\Support\Facades\Facade;

class FileUploader extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'file_uploader';
    }
}
