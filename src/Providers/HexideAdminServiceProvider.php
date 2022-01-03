<?php

namespace HexideDigital\HexideAdmin\Providers;

use HexideDigital\HexideAdmin\Classes\Breadcrumbs;
use HexideDigital\HexideAdmin\Classes\HexideAdmin;
use HexideDigital\HexideAdmin\Classes\Notifications\NotificationInterface;
use HexideDigital\HexideAdmin\Classes\Notifications\ToastrNotification;
use HexideDigital\HexideAdmin\Console\Commands\CreateAdminUser;
use HexideDigital\HexideAdmin\Console\Commands\HexideAdminCommand;
use HexideDigital\HexideAdmin\Http\ViewComposers\HexideAdminComposer;
use HexideDigital\HexideAdmin\Components\NavItems\LanguageItem;
use Illuminate\Container\Container;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\ServiceProvider;
use Route;

class HexideAdminServiceProvider extends ServiceProvider
{
    private array $commands = [
        HexideAdminCommand::class,
        CreateAdminUser::class,
    ];

    private array $components = [
        LanguageItem::class,
    ];

    public function register()
    {
        $this->app->singleton(HexideAdmin::class, function (Container $app) {
            return new HexideAdmin($app);
        });

        $this->app->singleton(Breadcrumbs::class, function () {
            return new Breadcrumbs();
        });

        $this->app->bind(NotificationInterface::class, function (){
            return new ToastrNotification();
        });
    }

    public function boot(Factory $view)
    {
        $this->loadPublishes();

        $this->loadConfig();
        $this->loadViews();
        $this->loadTranslations();
        $this->loadRoutes();

        $this->registerCommands();
        $this->registerComponents();
        $this->registerViewComposers($view);
    }

    private function loadPublishes()
    {
        $this->publishes([
            $this->packagePath("config/hexide-admin.php") => config_path('hexide-admin.php'),
            $this->packagePath("config/model-permissions.php") => config_path('model-permissions.php'),
            $this->packagePath("config/translatable.php") => config_path('translatable.php'),
        ], 'hexide-admin-configs');

        $this->publishes([
            $this->packagePath("resources/lang") => resource_path('lang/vendor/hexide_admin')
        ], 'hexide-admin-translations');

        $this->publishes([
            $this->packagePath("resources/views") => resource_path('views/vendor/hexide_admin')
        ], 'hexide-admin-translations');

        $this->publishes([
            $this->packagePath("src/Console/stubs") => base_path('stubs/hexide_admin')
        ], 'hexide-admin-stubs');

    }

    private function loadRoutes()
    {
        $routesCfg = [
            'as' => 'admin.',
            'prefix' => 'admin',
            'middleware' => ['web', 'auth:admin'],
        ];

        Route::group($routesCfg, function () {
            $this->loadRoutesFrom($this->packagePath('routes/admin.php'));
        });

        Route::group(['middleware' => ['web']], function () {
            $this->loadRoutesFrom($this->packagePath('routes/web.php'));
        });
    }

    private function loadConfig()
    {
        $this->mergeConfigFrom($this->packagePath("config/hexide-admin.php"), 'hexide_admin');
    }

    private function loadTranslations()
    {
        $this->loadTranslationsFrom($this->packagePath('resources/lang'), 'hexide_admin');
    }

    private function loadViews()
    {
        $this->loadViewsFrom($this->packagePath('resources/views'), 'hexide_admin');
    }

    private function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }
    }

    private function registerViewComposers(Factory $view)
    {
        $view->composer('admin.*', HexideAdminComposer::class);
        $view->composer('hexide_admin::*', HexideAdminComposer::class);
    }

    private function registerComponents()
    {
        // Support of x-components is only available for Laravel >= 7.x
        // versions. So, we check if we can load components.

        $canLoadComponents = method_exists(
            'Illuminate\Support\ServiceProvider',
            'loadViewComponentsAs'
        );

        if (! $canLoadComponents) {
            return;
        }

        $this->loadViewComponentsAs('hdadmin', $this->components);
    }

    /**
     * Get the absolute path to some package resource.
     *
     * @param string $path The relative path to the resource
     * @return string
     */
    private function packagePath(string $path): string
    {
        return __DIR__ . "/../../$path";
    }
}
