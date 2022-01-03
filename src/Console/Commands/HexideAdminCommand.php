<?php

namespace HexideDigital\HexideAdmin\Console\Commands;

use Arr;
use HexideDigital\ModelPermissions\Models\Permission;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class HexideAdminCommand extends BaseCommand
{
    protected $name = 'hd-admin:module';
    protected $description = 'Generate module files such as model, controllers, views etc.';

    /** @var array<string, string> */
    protected array $stubPaths = [
        'migration' => 'database',
        'model' => 'models',
        'request' => 'http/requests',
        'service' => 'service',
        'controller' => 'http/controllers',
        'livewire' => 'http/livewire',

        'lang' => 'lang',
        'views' => 'views',
        'routes' => 'routes',
        'menu_item' => 'menu_item',
    ];

    protected Repository $config;
    protected Filesystem $filesystem;

    protected bool $withInteract;
    /** @var array<string|int, string> */
    protected array $forceTypes;

    /** Camel cased ModuleName */
    protected string $moduleName;
    protected bool $translatable;
    protected Collection $namespaces;


    public function __construct(Repository $config, Filesystem $filesystem)
    {
        parent::__construct();

        $this->config = $config;
        $this->filesystem = $filesystem;
    }


    public function handle(): int
    {
        $this->namespaces = collect($this->config->get('hexide-admin.namespaces'));

        $this->withInteract = !$this->option('no-interaction');
        $this->setForceTypes();

        $this->info('Start: creating module files...');

        $this->setModuleName();
        $this->setTranslatable();

        $this->createFiles();
        $this->prepareResources();

        $this->info('Finish: files are created and ');

        return self::SUCCESS;
    }

    private function setForceTypes()
    {
        $this->forceTypes = [];

        if ($force = $this->option('force')) {
            if (empty($force)) {
                $force = 'all';
            }
            $this->forceTypes = explode(',', $force);
        }
    }

    private function setModuleName()
    {
        $name = Str::singular(Str::studly($this->argument('name')));

        if ($this->withInteract) {
            $name = $this->ask("Enter the module name", $name);
        }
        $this->info('Create module ' . $name);

        if (preg_match('([^A-Za-z0-9_/\\\\])', $name)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $this->moduleName = $name;
    }

    private function setTranslatable()
    {
        $transl = $this->option('translatable');

        if (!$this->option('translatable') && $this->withInteract) {
            $transl = $this->confirm('Create a model with translated fields?', false);
        }

        $this->translatable = $transl;
    }

    //--------------------------------------------------------

    private function createFiles(): void
    {
        $this->info('Creating files...');

        $methods = array_filter([
            'createModels' => ['start' => 'Models', 'finish' => 'Models',],
            'createMigrations' => ['start' => 'Migrations', 'finish' => 'Migrations',],
            'createService' => $this->option('service') ? ['start' => 'Service', 'finish' => 'Service',] : false,
            'createRequest' => ['start' => 'Request', 'finish' => 'Request',],
            'createController' => ['start' => 'Controller', 'finish' => 'Controller',],
            'createViews' => ['start' => 'Views', 'finish' => 'Views',],
            'createLivewire' => ['start' => 'Livewire table', 'finish' => 'Livewire table',],
        ]);

        foreach ($methods as $method => $points) {
            $this->info("Creating: " . $points['start']);

            $this->{$method}();

            $this->info("Finished: " . $points['finish']);
            $this->newLine();
        }

        $this->info('Files created.');
    }

    private function createModels(): void
    {
        $path = app_path('Models/');

        $type = $this->translatable ? '.with_translation' : '';

        $class = $this->getModuleName();

        $content = $this->getContent($this->resolveStubPath('model', "model$type.stub"), [
            "{{ namespace }}" => $this->getNamespace('model', 'App\\Models'),
            "{{ class }}" => $class,
        ]);

        if ($this->translatable) {
            $this->createTranslatedModel();
        }

        $this->makeClass($class, $path . $class . '.php', $content, $this->isForced('model'));
    }

    private function createTranslatedModel(): void
    {
        $path = app_path('Models/');

        $class = $this->getModuleName() . 'Translation';

        $content = $this->getContent($this->resolveStubPath('model', 'model.translation.stub'), [
            "{{ namespace }}" => $this->getNamespace('model', 'App\\Models'),
            "{{ parent_model }}" => $this->getSnakeCaseName(),
            "{{ class }}" => $class,
        ]);

        $this->makeClass($class, $path . $class . '.php', $content, $this->isForced('model'));
    }

    private function createMigrations(): void
    {
        $path = database_path('migrations/');

        $type = $this->translatable ? '.translation' : '';

        $name = 'Create' . $this->getModuleName(2) . 'Table';

        if ($this->migrationExists($name, $path)) {
            $this->warn("A $name class already exists");
        } else {
            $prefix = date('Y_m_d_His');

            $content = $this->getContent($this->resolveStubPath('migration', "/migration.create$type.stub"), [
                "{{ class }}" => $name,
                "{{ table }}" => $this->getSnakeCaseName(2),
                "{{ table_singular }}" => $this->getSnakeCaseName(),
            ]);

            $this->makeFile($path . $prefix . '_' . Str::snake($name) . '.php', $content, $this->isForced('migration'));
        }

        if ($this->hasOption('populate')) {
            $name = 'Populate' . $this->getModuleName(2) . 'Table';

            if ($this->migrationExists($name, $path)) {
                $this->warn("A $name class already exists");
            } else {
                $prefix = now()->addSecond()->format('Y_m_d_His');

                $content = $this->getContent($this->resolveStubPath('migration', "/migration.populate.stub"), [
                    "{{ class }}" => $name,
                    "{{ table }}" => $this->getSnakeCaseName(2),
                    "{{ table_singular }}" => $this->getSnakeCaseName(),
                    "{{ Model }}" => $this->getModuleName(),
                ]);

                $this->makeFile($path . $prefix . '_' . Str::snake($name) . '.php', $content, $this->isForced('migration'));
            }
        }
    }

    /**
     * Ensure that a migration with the given name doesn't already exist.
     *
     * @param string $name
     * @param string|null $migrationPath
     *
     * @return bool
     * @throws FileNotFoundException
     */
    protected function migrationExists(string $name, string $migrationPath = null): bool
    {
        if (!empty($migrationPath)) {
            $migrationFiles = $this->filesystem->glob($migrationPath . '*.php');

            foreach ($migrationFiles as $migrationFile) {
                $this->filesystem->requireOnce($migrationFile);
            }
        }

        if (class_exists($name)) {
            return true;
        }

        return false;
    }

    private function createService(): void
    {
        if ($this->option('service')) {
            $path = app_path('Services/Backend/');

            $class = $this->getModuleName() . 'Service';

            $content = $this->getContent($this->resolveStubPath('service', "/service.stub"), [
                "{{ namespace }}" => $this->getNamespace('service', 'App\\Services\\Backend'),
                "{{ model_namespace }}" => $this->getModelNamespace(),
                "{{ Model }}" => $this->getModuleName(),
                "{{ ModelService }}" => $class,
            ]);

            $this->makeDir($path);

            if (!$this->filesystem->isFile($path . "BaseService.php")) {
                $this->makeFile(
                    $path . 'BaseService.php',
                    $this->getContent($this->resolveStubPath('service', "/base.service.stub"), [
                        "{{ namespace }}" => $this->getNamespace('service', 'App\\Services\\Backend'),
                    ])
                );
            }

            $this->makeClass($class, $path . $class . '.php', $content, $this->isForced('service'));
        }
    }

    private function createRequest(): void
    {
        $path = app_path('Http/Requests/Backend/');

        $type = $this->translatable ? '.translation' : '';

        $class = $this->getModuleName() . 'Request';

        $content = $this->getContent($this->resolveStubPath('request', "/request.admin$type.stub"), [
            "{{ namespace }}" => $this->getNamespace('request', 'App\\Http\\Requests\\Backend'),
            "{{ model_namespace }}" => $this->getModelNamespace(),
            "{{ model }}" => $this->getSnakeCaseName(),
            "{{ ModuleName }}" => $this->getModuleName(),
            "{{ module_name }}" => $this->getSnakeCaseName(2),
            "{{ class }}" => $class,
        ]);

        $this->makeDir($path);

        $this->makeClass($class, $path . $class . '.php', $content, $this->isForced('request'));
    }

    private function createController(): void
    {
        $path = app_path('Http/Controllers/Backend/');

        $class = $this->getModuleName() . 'Controller';

        $service_type = $this->option('service') ? 'service' : 'default';

        $partials = [
            '{{ construct }}' => "construct/$service_type.stub",
            '{{ create_handle }}' => "create/$service_type.stub",
            '{{ update_handle }}' => "update/$service_type.stub",
        ];

        $content = $this->getContent($this->resolveStubPath('controller', "/controller.admin.stub"));

        foreach ($partials as $key => $stub) {
            $content = $this->getContent($content, [
                $key => $this->getContent($this->resolveStubPath('controller', $stub))
            ]);
        }

        $content = $this->getContent($content, [
            "{{ namespace }}" => $this->getNamespace('controller', 'App\\Http\\Controllers\\Backend'),
            "{{ model_namespace }}" => $this->getModelNamespace(),
            "{{ class }}" => $class,

            "{{ model }}" => $this->getModuleName(),
            "{{ model_variable }}" => $this->getSnakeCaseName(),
            "{{ modelVariable }}" => lcfirst($this->getModuleName()),

            "{{ model_table }}" => $this->getSnakeCaseName(2),
            "{{ model_translation_table }}" => $this->getSnakeCaseName() . '_translations',

            "//{{ service_namespace }}" => $this->option('service')
                ? "\nuse " . $this->getNamespace('service', 'App\\Services\\Backend') .
                '\\' . $this->getModuleName() . "Service;"
                : ''
        ]);

        $this->makeClass($class, $path . $class . '.php', $content, $this->isForced('controller'));
    }

    private function createViews(): void
    {
        $dir_path = base_path($this->config->get('hexide-admin.module_paths.views')) . $this->getSnakeCaseName(2);

        $force = $this->isForced('views');

        if (!$this->makeDir($dir_path, $force)) {
            $this->warn("Directory for model views already exists $dir_path");
        }

        $this->makeDir($dir_path . '/tabs', $force);
        $this->makeDir($dir_path . '/partials', $force);

        $stubs = array_filter([
            'create.stub' => 'create.blade.php',
            'edit.stub' => 'edit.blade.php',
            'index.stub' => 'index.blade.php',
            'show.stub' => 'show.blade.php',
            '_form.stub' => 'partials/_form.blade.php',
            'tabs/general.stub' => 'tabs/general.blade.php',
            'tabs/locale.stub' => $this->translatable ? 'tabs/locale.blade.php' : false,
        ]);

        $replaces = [
            'index.stub' => ["{{ model }}" => $this->getSnakeCaseName(),],
            '_form.stub' => ["{{ show_locale_tabs }}" => $this->translatable ? "true" : "false",],
            'show.stub' => ["{{ model_namespace }}" => "\\" . $this->getModelNamespace(),],
            'tabs/general.stub' => ["{{ model_namespace }}" => "\\" . $this->getModelNamespace(),],
            'tabs/locale.stub' => ["{{ model_namespace }}" => "\\" . $this->getModelNamespace(),],
        ];

        foreach ($stubs as $stub => $name) {
            $content = $this->getContent($this->resolveStubPath('views', $stub));

            if (in_array($stub, array_keys($replaces))) {
                $content = $this->getContent($content, $replaces[$stub]);
            }

            $this->makeFile($dir_path . '/' . $name, $content, $force);
        }
    }

    private function createLivewire(): void
    {
        $path = app_path('Http/Livewire/Admin/Tables/');

        $class = $this->getModuleName() . 'Table';

        $content = $this->getContent($this->resolveStubPath('livewire', "/table.stub"), [
            "{{ namespace }}" => $this->getNamespace('livewire-table', 'App\\Http\\Livewire\\Admin\\Tables'),
            "{{ model_namespace }}" => $this->getModelNamespace(),
            "{{ model }}" => $this->getSnakeCaseName(),
            "{{ ModuleName }}" => $this->getModuleName(),
            "{{ module_name }}" => $this->getSnakeCaseName(2),
            "{{ class }}" => $class,
        ]);

        $this->makeDir($path);

        $this->makeClass($class, $path . $class . '.php', $content, $this->isForced('livewire'));
    }

    //--------------------------------------------------------

    private function prepareResources(): void
    {
        if (!$this->option('resources')) {
            return;
        }

        $this->info('Start preparing resources...');

        $methods = array_filter([
            'appendRoutes' => [
                'start' => 'model routes into admin route file',
                'finish' => 'Routes',
            ],
            'appendMenuItem' => [
                'start' => 'MenuItem',
                'finish' => 'MenuItem',
            ],
            'appendMenuItemTranslations' => [
                'start' => 'MenuItemTranslations',
                'finish' => 'MenuItemTranslations',
            ],
            'appendTranslations' => [
                'start' => 'Translations',
                'finish' => 'Translations',
            ],
        ]);

        foreach ($methods as $method => $points) {
            $this->info("Appending: " . $points['start']);

            $this->{$method}();

            $this->info("Finished: " . $points['finish']);
            $this->newLine();
        }

        $this->info('Resource generating is completed.');
    }

    private function appendRoutes(): void
    {
        $path = base_path($this->config->get('hexide-admin.module_paths.admin_route'));

        if ($this->filesystem->isFile($path)) {
            $stubs = [
                'ajax.stub' => '/*hexide_admin_stub-ajax*/',
                'resource.stub' => '/*hexide_admin_stub-resource*/',
                'default.stub' => '/*hexide_admin_stub*/',
            ];

            $route_content = $this->getContent($path);
            foreach ($stubs as $stub => $replace) {
                $content = $this->getContent($this->resolveStubPath('routes', $stub), [
                    '{{ module_name }}' => $this->getSnakeCaseName(2),
                    '{{ ModelController }}' => $this->getModuleName() . 'Controller',
                ]);

                $route_content = $this->getContent($route_content, [$replace => $content,]);
            }

            $this->makeFile($path, $route_content, true);

            $this->info('Module routes appended.');
        } else {
            $this->warn('Admin routes not found.');
        }
    }

    private function appendMenuItem(): void
    {
        $path = config_path('adminlte.php');

        if ($this->filesystem->isFile($path)) {
            $content = $this->getContent($this->resolveStubPath('menu_item', "menu_item.stub"), [
                "{{ ModuleName }}" => $this->getModuleName(),
                "{{ module_name }}" => $this->getSnakeCaseName(2),
                "{{ module_name_access }}" => Permission::key($this->getSnakeCaseName(2), Permission::Access),
            ]);

            $this->makeFile($path, $this->getContent($path, ["/*hexide_admin_stub*/" => $content]), true);

            $this->info('Menu item appended.');
        } else {
            $this->warn('Can`t add menu item to file config/adminlte.php - file does not exist');
        }
    }

    private function appendMenuItemTranslations(): void
    {
        $locales = $this->config->get('hexide-admin.locales');
        list($path, $file_name) = $this->config->get('hexide-admin.module_paths.adminlte_menu_translations');

        $path = base_path($path);
        foreach ($locales as $locale) {
            $file_path = $path . "$locale/$file_name";

            if ($this->filesystem->isFile($file_path)) {
                $content = $this->getContent($this->resolveStubPath('menu_item', "menu_locale.stub"), [
                    "{{ module_name }}" => $this->getSnakeCaseName(2),
                    "{{ ModuleName }}" => $this->getModuleName(),
                ]);

                $this->makeFile($file_path, $this->getContent($file_path, ["/*hexide_admin_stub*/" => $content]), true);
            } else {
                $this->warn('can`t append menu translations for locale ' . $locale);
            }
        }
    }

    private function appendTranslations(): void
    {
        $locales = $this->config->get('hexide-admin.locales');
        list($path, $file_name) = $this->config->get('hexide-admin.module_paths.lang');

        $path = base_path($path);

        foreach ($locales as $locale) {
            $file_path = $path . "$locale/$file_name";

            if ($this->filesystem->isFile($file_path)) {
                $content = $this->getContent($this->resolveStubPath('lang', $locale . ".models.stub"), [
                    "{{ module_name }}" => $this->getSnakeCaseName(2),
                    "{{ ModuleName }}" => $this->getModuleName(),
                ]);

                $this->makeFile($file_path, $this->getContent($file_path, ["/*hexide_admin_stub*/" => $content]), true);
            } else {
                $this->warn('can`t append model translations for locale ' . $locale);
            }
        }
    }

    //--------------------------------------------------------

    protected function getModuleName(int $plural = 1): string
    {
        return $plural === 1 ? Str::singular($this->moduleName) : Str::plural($this->moduleName);
    }

    protected function getSnakeCaseName(int $plural = 1): string
    {
        $name = Str::snake($this->getModuleName());

        return $plural === 1 ? Str::singular($name) : Str::plural($name);
    }

    protected function getModelNamespace(): string
    {
        return $this->getNamespace('model') . '\\' . $this->getModuleName();
    }

    //--------------------------------------------------------

    protected function getNamespace($type, $default = null)
    {
        return $this->namespaces->get($type, $default);
    }

    protected function resolveStubPath($type, $stub): string
    {
        $stub = trim(Arr::get($this->stubPaths, $type), '/') . '/' . trim($stub, '/');

        return file_exists($customPath = $this->laravel->basePath('stubs/hexide_admin/' . $stub))
            ? $customPath
            : __DIR__ . "/../stubs/" . $stub;
    }

    protected function makeDir(string $path, bool $force = false): bool
    {
        if (!$this->filesystem->isDirectory($path) || $force) {
            return $this->filesystem->makeDirectory($path, 0755, true, $force);
        }
        return false;
    }

    protected function makeFile($file, $content, $force = false)
    {
        if ($this->withInteract && !$force && $this->filesystem->exists($file)) {
            $this->warn('File ' . $file . ' already exists');
            $force = $this->confirm('Are you sure you want to overwrite this existing file?', false);
        }

        if (!$this->filesystem->exists($file) || $force) {
            return $this->filesystem->put($file, $content);
        }

        return false;
    }

    protected function makeClass($class, $path, $content, $force = false)
    {
        if ($this->makeFile($path, $content, $force)) {
            $this->info($class . ' created.', 'vv');
        } else {
            $this->warn($class . ' not created.');
        }
    }

    protected function isForced($type): bool
    {
        if (empty($this->forceTypes)) {
            return false;
        }

        if (in_array('all', $this->forceTypes) || in_array($type, $this->forceTypes)) {
            return true;
        }

        return false;
    }

    /**
     * Get content from file and process replaces
     *
     * @param string $content file path or existed content
     * @param array $replacements
     * @return array|string|string[]
     * @throws FileNotFoundException
     */
    protected function getContent(string $content, array $replacements = [])
    {
        if ($this->filesystem->isFile($content)) {
            $content = $this->filesystem->get($content);
        }

        if (!empty($replacements)) {
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        }

        return $content;
    }

    //--------------------------------------------------------

    protected function getArguments(): array
    {
        return [
            new InputArgument('name', InputArgument::OPTIONAL, 'Module name in title camel cased type ex. ModuleName'),
        ];
    }

    protected function getOptions(): array
    {
        return [
            new InputOption('service', 's', InputOption::VALUE_NONE, 'Generate with service class'),
            new InputOption('translatable', 't', InputOption::VALUE_NONE, 'Generate files with translatable attributes'),
            new InputOption('populate', null, InputOption::VALUE_NONE, 'Generate populate migration for model'),
            new InputOption('resources', '-r', InputOption::VALUE_NONE, 'Enable appending and modify resource filed (lang,menu,routes,models)'),
            new InputOption('force', 'f', InputOption::VALUE_OPTIONAL, 'Overwrite all existing module files or for only defined types of files (livewire,controller,request,model,service,views)', 'all'),
        ];
    }
}
