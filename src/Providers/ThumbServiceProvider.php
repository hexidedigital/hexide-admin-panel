<?php

namespace HexideDigital\HexideAdmin\Providers;

use HexideDigital\HexideAdmin\Classes\Thumb;
use Illuminate\Support\ServiceProvider;

class ThumbServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('thumb', Thumb::class);
    }
}