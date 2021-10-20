<?php

namespace HexideDigital\HexideAdmin\Providers;

use Illuminate\Support\ServiceProvider;

class HexideAdminServiceProvider extends ServiceProvider
{

    /**
     * Boot the instance.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/hexide_admin.php' => config_path('hexide_admin.php'),
        ], 'hexide_admin');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/hexide_admin.php', 'hexide_admin');
    }

}
