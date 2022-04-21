<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Providers;

use HexideDigital\HexideAdmin\Classes\Breadcrumbs;
use HexideDigital\HexideAdmin\Classes\Configurations\Configuration;
use HexideDigital\HexideAdmin\Classes\FileUploader;
use HexideDigital\HexideAdmin\Classes\HexideAdmin;
use HexideDigital\HexideAdmin\Classes\Notifications\NotificationInterface;
use HexideDigital\HexideAdmin\Classes\Notifications\ToastrNotification;
use HexideDigital\HexideAdmin\Classes\Thumb;
use HexideDigital\HexideAdmin\Components\NavItems;
use HexideDigital\HexideAdmin\Components\Tabs;
use HexideDigital\HexideAdmin\Console\Commands\CleanSeededStorageCommand;
use HexideDigital\HexideAdmin\Console\Commands\CreateAdminUser;
use HexideDigital\HexideAdmin\Console\Commands\Creators;
use HexideDigital\HexideAdmin\Console\Commands\ModuleMakeCommand;
use HexideDigital\HexideAdmin\Console\Commands\PrepareDeployCommand;
use HexideDigital\HexideAdmin\Console\Commands\SetupProjectCommand;
use HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables;
use HexideDigital\HexideAdmin\Http\ViewComposers\HexideAdminComposer;
use HexideDigital\HexideAdmin\Services\Backend\UserService;
use Illuminate\Container\Container;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Route;

class HexideAdminServiceProvider extends ServiceProvider
{
    private array $commands = [
        CreateAdminUser::class,
        PrepareDeployCommand::class,
        CleanSeededStorageCommand::class,
        SetupProjectCommand::class,

        ModuleMakeCommand::class,
        Creators\ControllerMakeCommand::class,
        Creators\MigrationMakeCommand::class,
        Creators\LivewireTableMakeCommand::class,
        Creators\ModelMakeCommand::class,
        Creators\ModelTranslationMakeCommand::class,
        Creators\PolicyMakeCommand::class,
        Creators\RequestMakeCommand::class,
        Creators\ServiceMakeCommand::class,
    ];

    private array $components = [
        'language-item' => NavItems\LanguageItem::class,
        'tabs-component' => Tabs\TabsComponent::class,
    ];

    private array $livewire = [
        'admin' => [
            'tables' => [
                'configuration-table' => Tables\ConfigurationTable::class,
                'permission-table' => Tables\PermissionTable::class,
                'role-table' => Tables\RoleTable::class,
                'user-table' => Tables\UserTable::class,
                'translation-table' => Tables\TranslationTable::class,
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

        $this->app->bind(UserService::class, function () {
            return new UserService();
        });

        $this->app->bind('thumb', Thumb::class);
        $this->app->bind('file_uploader', FileUploader::class);
    }

    public function boot(Factory $view)
    {
        $this->loadPublishes();

        $this->loadConfig();
        $this->loadViews();
        $this->loadTranslations();
        $this->loadRoutes();

        $this->registerBladeDirectives();
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
            $this->packagePath('build') => public_path('vendor/hexide-admin/build'),
            $this->packagePath('resources/js') => resource_path('js'),
            $this->packagePath('resources/img') => resource_path('img'),
            $this->packagePath('resources/sass') => resource_path('sass'),
        ], 'hexide-admin:asset');

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
            'prefix' => config('hexide-admin.routes.admin.prefix', 'admin'),
            'middleware' => config('hexide-admin.routes.admin.middleware', ['web', 'auth:admin', 'language:admin']),
        ];

        Route::group($routesCfg, function () {
            $this->loadRoutesFrom($this->packagePath('routes/admin.php'));
        });

        Route::group(['middleware' => ['web']], function () {
            $this->loadRoutesFrom($this->packagePath('routes/web.php'));
        });

        Route::middleware(config('hexide-admin.routes.admin.middleware'))
            ->as('admin.')
            ->prefix(config('hexide-admin.routes.admin.prefix'))
            ->group(base_path('routes/admin.php'));
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

    private function registerBladeDirectives()
    {
        /* @admin */
        Blade::if('admin', function () {
            return Auth::check() && Auth::user()->hasAdminAccess();
        });

        /* @isRole */
        Blade::if('isRole', function (int $roleId) {
            return Auth::check() && Auth::user()->isRole($roleId);
        });
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
