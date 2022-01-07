<?php

namespace HexideDigital\HexideAdmin\Providers;

use Illuminate\Support\ServiceProvider;
use HexideDigital\HexideAdmin\Classes\FileUploader;

class FileUploaderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('file_uploader', FileUploader::class);
    }
}
