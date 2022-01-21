<?php

namespace HexideDigital\HexideAdmin\Providers;

use HexideDigital\HexideAdmin\Classes\Configurations\Configuration;
use HexideDigital\HexideAdmin\Classes\Breadcrumbs;
use HexideDigital\HexideAdmin\Classes\HexideAdmin;
use HexideDigital\HexideAdmin\Classes\Notifications\NotificationInterface;
use HexideDigital\HexideAdmin\Classes\Notifications\ToastrNotification;
use HexideDigital\HexideAdmin\Components\NavItems\LanguageItem;
use HexideDigital\HexideAdmin\Components\Tabs\TabsComponent;
use HexideDigital\HexideAdmin\Console\Commands\CleanSeededStorageCommand;
use HexideDigital\HexideAdmin\Console\Commands\CreateAdminUser;
use HexideDigital\HexideAdmin\Console\Commands\HexideAdminCommand;
use HexideDigital\HexideAdmin\Console\Commands\PrepareDeployCommand;
use HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables\ConfigurationTable;
use HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables\PermissionTable;
use HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables\RoleTable;
use HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables\UserTable;
use HexideDigital\HexideAdmin\Http\ViewComposers\HexideAdminComposer;
use HexideDigital\HexideAdmin\Models\AdminConfiguration;
use Illuminate\Container\Container;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Route;

class HexideAdminServiceProvider extends ServiceProvider
{
    private array $commands = [
        HexideAdminCommand::class,
        CreateAdminUser::class,
        PrepareDeployCommand::class,
        CleanSeededStorageCommand::class,
    ];

    private array $components = [
        'language-item' => LanguageItem::class,
        'tabs-component' => TabsComponent::class,
    ];

    private array $livewire = [
        'admin' => [
            'tables' => [
                'configuration-table' => ConfigurationTable::class,
                'permission-table' => PermissionTable::class,
                'role-table' => RoleTable::class,
                'user-table' => UserTable::class,
            ],
        ],
    ];

    public function register()
    {
        $this->app->singleton(HexideAdmin::class, function (Container $app) {
            return new HexideAdmin($app);
        });

        $this->app->singleton(Breadcrumbs::class, function () {
            return new Breadcrumbs();
        });

        $this->app->bind(NotificationInterface::class, function () {
            return new ToastrNotification();
        });

        $this->app->bind(Configuration::class, function () {
            return new Configuration();
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
        $this->registerLivewireComponents();
    }

    private function loadPublishes()
    {
        $this->publishes([
            $this->packagePath("config/hexide-admin.php") => config_path('hexide-admin.php'),
        ], 'hexide-admin:configs');

        $this->publishes([
            $this->packagePath("resources/lang") => resource_path('lang/vendor/hexide-admin'),
        ], 'hexide-admin:translations');

        $this->publishes([
            $this->packagePath("resources/views") => resource_path('views/vendor/hexide-admin'),
        ], 'hexide-admin:views');

        $this->publishes([
            $this->packagePath('resources/build') => public_path('vendor/hexide-admin/build'),
            $this->packagePath('resources/js') => resource_path('js'),
            $this->packagePath('resources/img') => resource_path('img'),
            $this->packagePath('resources/sass') => resource_path('sass'),
        ], 'hexide-admin:public');

        $this->publishes([
            $this->packagePath("src/Console/stubs") => base_path('stubs/hexide-admin'),
        ], 'hexide-admin:stubs');

        $this->publishes([
            $this->packagePath('database/migrations') => database_path('migrations'),
            $this->packagePath('database/seeders') => database_path('seeders'),
        ], 'hexide-admin:database');
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
        $this->mergeConfigFrom($this->packagePath("config/hexide-admin.php"), 'hexide-admin');
    }

    private function loadTranslations()
    {
        $this->loadTranslationsFrom($this->packagePath('resources/lang'), 'hexide-admin');
    }

    private function loadViews()
    {
        $this->loadViewsFrom($this->packagePath('resources/views'), 'hexide-admin');
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
        $view->composer('hexide-admin::*', HexideAdminComposer::class);
    }

    private function registerComponents()
    {
        // Support of x-components is only available for Laravel >= 7.x
        // versions. So, we check if we can load components.
        $canLoadComponents = method_exists(ServiceProvider::class, 'loadViewComponentsAs');

        if (!$canLoadComponents) {
            return;
        }

        $this->loadViewComponentsAs('hexide-admin', $this->components);

        Blade::componentNamespace('HexideDigital\\HexideAdmin\\Components', 'hexide-admin');
    }

    private function registerLivewireComponents()
    {
        foreach (array_dot($this->livewire) as $alias => $component) {
            Livewire::component('hexide-admin::' . $alias, $component);
        }
    }

    /**
     * Get the absolute path to some package resource.
     *
     * @param string $path The relative path to the resource
     *
     * @return string
     */
    private function packagePath(string $path): string
    {
        return __DIR__ . "/../../$path";
    }
}
