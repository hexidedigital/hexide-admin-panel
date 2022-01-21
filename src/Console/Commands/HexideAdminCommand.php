<?php

namespace HexideDigital\HexideAdmin\Console\Commands;

use HexideDigital\ModelPermissions\Models\Permission;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class HexideAdminCommand extends BaseCommand
{
    private Collection $replaceMap;

    protected $name = 'hd-admin:make:module';
    protected $description = 'Generate module files such as model, controllers, views etc.';

    /** @var array<string, string> */
    protected array $stubPaths = [
        'migration' => 'database',
        'model' => 'models',
        'request' => 'http/requests',
        'service' => 'service',
        'policy' => 'policies',
        'controller' => 'http/controllers',
        'livewire' => 'http/livewire',

        'lang' => 'lang',
        'views' => 'views',
        'routes' => 'routes',
        'menu_item' => 'menu_item',
    ];

    protected Filesystem $filesystem;

    protected Collection $forceTypes;

    /** Camel cased ModuleName */
    protected string $moduleName;
    protected bool $translatable;
    protected Collection $namespaces;


    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }


    /** @throws FileNotFoundException */
    public function handle(): int
    {
        $this->namespaces = collect(config('hexide-admin.namespaces'));

        $this->setForceTypesOption();

        $this->info('Start: creating module files...');

        $this->setModuleName();
        $this->setTranslatableOption();
        $this->setGlobalReplaces();

        $this->createFiles();
        $this->prepareResources();

        $this->info('Finish: module files are created.');

        return self::SUCCESS;
    }

    private function setForceTypesOption()
    {
        $this->forceTypes = collect();

        if ($force = $this->option('force')) {
            if (empty($force)) {
                $force = 'all';
            }

            $this->forceTypes = collect(explode(',', $force));
        }
    }

    private function setModuleName()
    {
        $name = Str::singular(Str::studly($this->argument('name')));

        $name = $this->ask('Enter the module name', $name);

        $this->info('Create module ' . $name);

        if (empty($name) || preg_match('([^A-Za-z0-9_/\\\\])', $name)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $this->moduleName = $name;
    }

    private function setTranslatableOption()
    {
        $transl = $this->confirm('Create a model with translated fields?', $this->option('translatable'));

        $this->translatable = $transl;
    }

    //--------------------------------------------------------

    /** @throws FileNotFoundException */
    private function createFiles(): void
    {
        $this->info('Creating files...');

        $methods = array_filter([
            'createModels' => ['start' => 'Models', 'finish' => 'Models',],
            'createMigrations' => ['start' => 'Migrations', 'finish' => 'Migrations',],
            'createService' => $this->option('service') ? ['start' => 'Service', 'finish' => 'Service',] : false,
            'createRequest' => ['start' => 'Request', 'finish' => 'Request',],
            'createPolicy' => ['start' => 'Policy', 'finish' => 'Policy',],
            'createController' => ['start' => 'Controller', 'finish' => 'Controller',],
            'createViews' => ['start' => 'Views', 'finish' => 'Views',],
            'createLivewire' => ['start' => 'Livewire table', 'finish' => 'Livewire table',],
        ]);

        foreach ($methods as $method => $points) {
            $this->info('Creating: ' . $points['start']);

            $this->{$method}();

            $this->info('Finished: ' . $points['finish']);
            $this->newLine();
        }

        $this->info('Files created.');
    }

    /** @throws FileNotFoundException */
    private function createModels(): void
    {
        $path = app_path('Models/');

        $type = $this->translatable ? '.with_translation' : '';

        $class = $this->getModuleName();

        $content = $this->getContentWithReplace($this->resolveStubPath('model', "model$type.stub"), [
            '{{ namespace }}' => $this->getNamespace('model'),
            '{{ class }}' => $class,
        ]);

        if ($this->translatable) {
            $this->createTranslatedModel();
        }

        $this->makeClass($class, $path . $class . '.php', $content, $this->isForced('model'));
    }

    /** @throws FileNotFoundException */
    private function createTranslatedModel(): void
    {
        $path = app_path('Models/');

        $class = $this->getModuleName() . 'Translation';

        $content = $this->getContentWithReplace($this->resolveStubPath('model', 'model.translation.stub'), [
            '{{ namespace }}' => $this->getNamespace('model'),
            '{{ class }}' => $class,
        ]);

        $this->makeClass($class, $path . $class . '.php', $content, $this->isForced('model'));
    }

    /** @throws FileNotFoundException */
    private function createMigrations(): void
    {
        $path = database_path('migrations/');

        $type = $this->translatable ? '.translation' : '';

        $class = 'Create' . $this->getModuleName(2) . 'Table';

        if ($this->migrationExists($class, $path)) {
            $this->warn("A migration $class class already exists");
        } else {
            $content = $this->getContentWithReplace($this->resolveStubPath('migration', "/migration.create$type.stub"), [
                '{{ class }}' => $class,
            ]);

            $this->makeFileOrPutContent($path . date('Y_m_d_His') . '_' . Str::snake($class) . '.php', $content, $this->isForced('migration'));
        }
    }

    /** @throws FileNotFoundException */
    private function createService(): void
    {
        if (!$this->option('service')) {
            return;
        }

        $path = app_path('Services/Backend/');

        $class = $this->getModuleName() . 'Service';

        $content = $this->getContentWithReplace($this->resolveStubPath('service', '/service.stub'), [
            '{{ namespace }}' => $this->getNamespace('service'),
            '{{ class }}' => $class,
        ]);

        $this->makeDir($path);

        $this->makeClass($class, $path . $class . '.php', $content, $this->isForced('service'));
    }

    /** @throws FileNotFoundException */
    private function createRequest(): void
    {
        $path = app_path('Http/Requests/Backend/');

        $type = $this->translatable ? '.translation' : '';

        $class = $this->getModuleName() . 'Request';

        $content = $this->getContentWithReplace($this->resolveStubPath('request', "/request.admin$type.stub"), [
            '{{ namespace }}' => $this->getNamespace('request'),
            '{{ class }}' => $class,
        ]);

        $this->makeDir($path);

        $this->makeClass($class, $path . $class . '.php', $content, $this->isForced('request'));
    }

    /** @throws FileNotFoundException */
    private function createPolicy(): void
    {
        $path = app_path('Policies/');

        $class = $this->getModuleName() . 'Policy';

        $content = $this->getContentWithReplace($this->resolveStubPath('policy', '/policy.stub'), [
            '{{ namespace }}' => $this->getNamespace('policy'),
            '{{ class }}' => $class,
        ]);

        $this->makeDir($path);

        $this->makeClass($class, $path . $class . '.php', $content, $this->isForced('policy'));
    }

    /** @throws FileNotFoundException */
    private function createController(): void
    {
        $path = app_path('Http/Controllers/Backend/');

        $class = $this->getModuleName() . 'Controller';

        $content = $this->getContentWithReplace($this->resolveStubPath('controller', '/controller.admin.stub'), [
            '{{ namespace }}' => $this->getNamespace('controller'),
            '{{ class }}' => $class,
        ]);

        $this->makeClass($class, $path . $class . '.php', $content, $this->isForced('controller'));
    }

    /** @throws FileNotFoundException */
    private function createViews(): void
    {
        $dir_path = base_path(config('hexide-admin.module_paths.views')) . $this->getSnakeCaseName(2);

        $force = $this->isForced('views');

        if (!$this->makeDir($dir_path, $force)) {
            $this->warn("Directory for model views already exists $dir_path");
        }

        $this->makeDir($dir_path . '/tabs', $force);
        $this->makeDir($dir_path . '/partials', $force);

        $stubs = array_filter([
            'show.stub' => 'show.blade.php',
            '_form.stub' => 'partials/_form.blade.php',
            'tabs/general.stub' => 'tabs/general.blade.php',
            'tabs/locale.stub' => $this->translatable ? 'tabs/locale.blade.php' : false,
        ]);

        $replaces = [
            '_form.stub' => ['{{ show_locale_tabs }}' => $this->translatable ? 'true' : 'false',],
            'show.stub' => [],
            'tabs/general.stub' => [],
            'tabs/locale.stub' => [],
        ];

        foreach ($stubs as $stub => $name) {
            $content = $this->getContent($this->resolveStubPath('views', $stub));

            if (in_array($stub, array_keys($replaces))) {
                $content = $this->getContentWithReplace($content, $replaces[$stub]);
            }

            $this->makeFileOrPutContent($dir_path . '/' . $name, $content, $force);
        }
    }

    /** @throws FileNotFoundException */
    private function createLivewire(): void
    {
        $path = app_path('Http/Livewire/Admin/Tables/');

        $class = $this->getModuleName() . 'Table';

        $content = $this->getContentWithReplace($this->resolveStubPath('livewire', '/table.stub'), [
            '{{ namespace }}' => $this->getNamespace('livewire-table'),
            '{{ class }}' => $class,
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
            $this->info('Appending: ' . $points['start']);

            $this->{$method}();

            $this->info('Finished: ' . $points['finish']);
            $this->newLine();
        }

        $this->info('Resource generating is completed.');
    }

    /** @throws FileNotFoundException */
    private function appendRoutes(): void
    {
        $path = base_path(config('hexide-admin.module_paths.admin_route'));

        if (!$this->filesystem->isFile($path)) {
            $this->warn('Admin routes not found.');

            return;
        }

        $stubs = [
            'namespace.stub' => '/*hexide_admin_stub-namespace*/',
            'ajax.stub' => '/*hexide_admin_stub-ajax*/',
            'resource.stub' => '/*hexide_admin_stub-resource*/',
            'default.stub' => '/*hexide_admin_stub*/',
        ];

        $route_content = $this->getContent($path);
        foreach ($stubs as $stub => $replace) {
            $content = $this->getContentWithReplace($this->resolveStubPath('routes', $stub), [
                '{{ use_controller }}' => $this->getNamespace('controller') . '\\' . $this->getModuleName() . 'Controller',
            ]);

            $route_content = $this->getContentWithReplace($route_content, [$replace => $content,]);
        }

        $this->makeFileOrPutContent($path, $route_content, true);
    }

    /** @throws FileNotFoundException */
    private function appendMenuItem(): void
    {
        $path = config_path('adminlte.php');

        if (!$this->filesystem->isFile($path)) {
            $this->warn('Can`t add menu item to file config/adminlte.php - file does not exist');

            return;
        }

        $content = $this->getContentWithReplace($this->resolveStubPath('menu_item', 'menu_item.stub'), [
            '{{ module_name_access }}' => Permission::key($this->getSnakeCaseName(2), Permission::ViewAny),
        ]);

        $this->makeFileOrPutContent($path, $this->getContentWithReplace($path, ['/*hexide_admin_stub*/' => $content]), true);
    }

    /** @throws FileNotFoundException */
    private function appendMenuItemTranslations(): void
    {
        $locales = config('hexide-admin.locales');
        list($path, $file_name) = config('hexide-admin.module_paths.adminlte_menu_translations');

        $path = base_path($path);
        foreach ($locales as $locale) {
            $file_path = $path . "$locale/$file_name";

            if (!$this->filesystem->isFile($file_path)) {
                $this->warn('Can`t append menu translations for locale ' . $locale);

                continue;
            }

            $content = $this->getContentWithReplace($this->resolveStubPath('menu_item', 'menu_locale.stub'));

            $this->makeFileOrPutContent($file_path, $this->getContentWithReplace($file_path, ['/*hexide_admin_stub*/' => $content]), true);
        }
    }

    /** @throws FileNotFoundException */
    private function appendTranslations(): void
    {
        $locales = config('hexide-admin.locales');
        list($path, $file_name) = config('hexide-admin.module_paths.lang');

        $path = base_path($path);

        foreach ($locales as $locale) {
            $file_path = $path . "$locale/$file_name";

            if (!$this->filesystem->isFile($file_path)) {
                $this->warn('Can`t append model translations for locale ' . $locale);

                continue;
            }

            $content = $this->getContentWithReplace($this->resolveStubPath('lang', $locale . '.models.stub'));

            $this->makeFileOrPutContent($file_path, $this->getContentWithReplace($file_path, ['/*hexide_admin_stub*/' => $content]), true);
        }
    }

    //--------------------------------------------------------

    /** Studly case */
    protected function getModuleName(int $plural = 1): string
    {
        return $plural === 1 ? Str::singular($this->moduleName) : Str::plural($this->moduleName);
    }

    protected function getSnakeCaseName(int $plural = 1): string
    {
        $name = Str::snake($this->getModuleName());

        return $plural === 1 ? Str::singular($name) : Str::plural($name);
    }

    protected function getCamelCaseName(int $plural = 1): string
    {
        $name = Str::camel($this->getModuleName());

        return $plural === 1 ? Str::singular($name) : Str::plural($name);
    }

    protected function getKebabCaseName(int $plural = 1): string
    {
        $name = Str::kebab($this->getModuleName());

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

    // todo
    protected function directoryByNamespace($type)
    {
        $namespace = $this->getNamespace($type);


    }

    protected function resolveStubPath($type, $stub): string
    {
        $stub = trim(Arr::get($this->stubPaths, $type), '/') . '/' . trim($stub, '/');

        return file_exists($customPath = $this->laravel->basePath('stubs/hexide-admin/' . $stub))
            ? $customPath
            : __DIR__ . '/../stubs/' . $stub;
    }

    protected function makeDir(string $path, bool $force = false): bool
    {
        if (!$this->filesystem->isDirectory($path) || $force) {
            return $this->filesystem->makeDirectory($path, 0755, true, $force);
        }

        return false;
    }

    protected function makeFileOrPutContent(string $file, string $content, bool $force = false)
    {
        $exists = $this->filesystem->exists($file);

        if ($exists && !$force) {
            $this->warn('File ' . $file . ' already exists');

            $force = $this->confirm('Are you sure you want to overwrite this existing file?', false);
        }

        if (!$exists || $force) {
            return $this->filesystem->put($file, $content);
        }

        return false;
    }

    protected function makeClass($class, $path, $content, $force = false)
    {
        if ($this->makeFileOrPutContent($path, $content, $force)) {
            $this->info($class . ' created.', 'v');
        } else {
            $this->warn($class . ' not created.');
        }
    }

    protected function isForced($type): bool
    {
        if ($this->forceTypes->isEmpty()) {
            return false;
        }

        return $this->forceTypes->contains($type) || $this->forceTypes->contains('all');
    }

    /**
     * Ensure that a migration with the given name doesn't already exist.
     *
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

    /**
     * Get content from file and process replaces
     *
     * @throws FileNotFoundException
     */
    protected function getContent(string $content): string
    {
        if ($this->filesystem->isFile($content)) {
            $content = $this->filesystem->get($content);
        }

        return $content;
    }

    /** @throws FileNotFoundException */
    protected function getContentWithReplace(string $content, array $replacements = []): string
    {
        if ($this->filesystem->isFile($content)) {
            $content = $this->filesystem->get($content);
        }

        $replacements = $this->replaceMap->merge($replacements);

        return str_replace($replacements->keys(), $replacements->values(), $content);
    }

    protected function setGlobalReplaces()
    {
        $this->replaceMap = collect([
            '{{ model_namespace }}' => $this->getModelNamespace(),

            '{{ ModelName }}' => $this->getModuleName(),
            '{{ ModelNames }}' => $this->getModuleName(2),

            '{{ model-name }}' => $this->getKebabCaseName(),
            '{{ model-names }}' => $this->getKebabCaseName(2),

            '{{ model_name }}' => $this->getSnakeCaseName(),
            '{{ model_names }}' => $this->getSnakeCaseName(2),

            '{{ table }}' => $this->getSnakeCaseName(2),
            '{{ table_singular }}' => $this->getSnakeCaseName(),

            '{{ parent_model }}' => $this->getSnakeCaseName(),
        ]);
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
            new InputOption('resources', '-r', InputOption::VALUE_NONE, 'Enable appending and modify resource filed (lang,menu,routes,models)'),
            new InputOption('force', 'f', InputOption::VALUE_OPTIONAL, 'Overwrite all existing module files or for only defined types of files (livewire,controller,request,policy,model,service,views)', 'all'),
        ];
    }
}
