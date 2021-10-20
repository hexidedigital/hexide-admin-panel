<?php

namespace HexideDigital\HexideAdmin\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/admin';

    public function boot()
    {
        $path = __DIR__.'/../../routes/';

        $this->routes(function () use ($path) {
            Route::middleware('web')
                ->group($path.'hexide_admin.php');

            Route::middleware('web')
                ->group($path.'hexide_web.php');
        });
    }

}
