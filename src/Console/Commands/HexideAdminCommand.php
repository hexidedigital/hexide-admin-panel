<?php

namespace HexideDigital\HexideAdmin\Console\Commands;


use Arr;
use HexideDigital\ModelPermissions\Models\Permission;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class HexideAdminCommand extends BaseCommand
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'hexide_admin:module';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate module files such as model, controllers, views etc.';

    protected array $stub_paths = [
        'migration' => 'database',
        'model' => 'models',
        'request' => 'http/requests',
        'service' => 'service',
        'controller' => 'http/controllers',

        'lang' => 'lang',
        'views' => 'views',
        'routes' => 'routes',
        'menu_item' => 'menu_item',
    ];

    protected Repository $config;
    protected Filesystem $filesystem;

    protected bool $with_interact;
    protected array $force_types;

    /**
     * Camel cased ModuleName
     *
     * @var string
     */
    protected string $module_name;
    protected bool $translatable;

    protected Collection $namespaces;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Repository $config, Filesystem $filesystem)
    {
        parent::__construct();

        $this->config = $config;
        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->namespaces = collect($this->config->get('hexide_admin.namespaces'));

        $this->with_interact = !$this->option('no-interaction');
        $this->setForceTypes();

        $this->start();

        $this->setModuleName();
        $this->setTranslatable();

        $this->createFiles();
        $this->prepareResources();

        $this->end();

        return self::SUCCESS;
    }

    private function setForceTypes()
    {
        $this->force_types = [];

        if ($this->hasOption('force')) {
            $force = $this->option('force');
            if (empty($force)) {
                $force = 'all';
            }
            $this->force_types = explode(',', $force);
        }
    }

    private function setModuleName()
    {
        $name = Str::singular(Str::studly($this->argument('name')));

        if ($this->with_interact) {
            $name = $this->ask("Enter the module name", $name);
        }
        $this->info('Create module ' . $name);

        $this->module_name = $name;
    }

    private function setTranslatable()
    {
        $transl = $this->option('translatable');

        if (!$this->option('translatable') && $this->with_interact) {
            $transl = $this->confirm('Create a model with translated fields?', false);
        }

        $this->translatable = $transl;
    }

    //--------------------------------------------------------

    private function createFiles()
    {
        $this->warn('Creating files...');

//        $this->createModels();
//        $this->createMigrations();
//        $this->createService();
//        $this->createRequest();
//        $this->createController();
//        $this->createViews();

        $this->warn('Files created.');
    }

    private function createModels()
    {
        $this->info('Creating models...');

        $namespace = $this->getNamespace('model', 'App\\Models');

        $type = $this->translatable ? '.with_translation' : '';
        $stub = $this->resolveStubPath('model', "model$type.stub");

        if ($this->translatable) {
            $translation = $this->resolveStubPath('model', 'model.translation.stub');
            $this->createTranslatedModel();
        }

        $file = app_path('/Models/') . $this->getModuleName();


        $this->warn('Models created.');
    }

    private function createTranslatedModel()
    {
        $this->info('Creating model translation...');

        $path = app_path('/Models');
        $namespace = $this->getNamespace('model', 'App\\Models');

        $this->warn('Model translation created.');
    }

    private function createMigrations()
    {
        $this->info('Creating migration...');

        $path = database_path('/migrations');

        $type = $this->translatable ? '.translation' : '';
        $stub = $this->resolveStubPath('migration', "/migration.create$type.stub");

        if ($this->hasOption('populate')) {
            $stub = $this->resolveStubPath('migration', "/migration.populate.stub");
        }

        $this->warn('Migration created.');
    }

    private function createService()
    {
        if ($this->hasOption('service')) {
            $this->info('Creating service...');

            $path = app_path('Services/Backend/');

            $name = $this->getModuleName() . 'Service';

            $content = $this->getContent($this->resolveStubPath('service', "/service.stub"), [
                "{{ namespace }}" => $this->getNamespace('service', 'App\\Services\\Backend'),
                "{{ model_namespace }}" => $this->getModelNamespace(),
                "{{ Model }}" => $this->getModuleName(),
                "{{ ModelService }}" => $name,
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

            if ($this->makeFile($path . $name . '.php', $content)) {
                $this->warn($name . ' created.');
            } else {
                $this->warn($name . ' not created.');
            }
        }
    }

    private function createRequest()
    {
        $this->info('Creating request...');

        $path = app_path('/Http/Requests/Backend');
        $namespace = $this->getNamespace('request', 'App\\Http\\Requests\\Backend');

        $type = $this->translatable ? '.translation' : '';
        $stub = $this->resolveStubPath('request', "/request.admin$type.stub");

        $this->warn('Request created.');
    }

    private function createController()
    {
        $this->info('Creating controller...');

        $path = app_path('/Http/Controllers/Backend');
        $namespace = $this->getNamespace('controller', 'App\\Http\\Controllers\\Backend');

        $stub = $this->resolveStubPath('controller', "/controller.admin.stub");

        $service_type = $this->hasOption('service') ? 'service' : 'default';
        $translation_type = $this->translatable ? 'translation' : 'default';
        $partials = collect([
            'construct' => "$service_type.stub",
            'create' => "$service_type.stub",
            'update' => "$service_type.stub",

            'index' => "$translation_type.stub",
        ]);


        $this->warn('Controller created.');
    }

    //--------------------------------------------------------

    private function prepareResources()
    {
        $this->warn('Start preparing resources...');

//        $this->appendRoutes();
//        $this->appendMenuItem();
        $this->appendMenuItemTranslations();
        $this->appendTranslations();

        $this->warn('Resource generating is completed.');
    }

    private function appendRoutes()
    {
        $this->info('Appending model routes into admin route file ...');

        $path = base_path($this->config->get('hexide_admin.module_paths.admin_route'));

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

            $this->warn('Module routes appended.');
        } else {
            $this->error('Admin routes not found.');
        }
    }

    private function appendMenuItem()
    {
        $this->info('Appending menu item...');

        $path = config_path('adminlte.php');

        if ($this->filesystem->isFile($path)) {
            $content = $this->getContent($this->resolveStubPath('menu_item', "menu_item.stub"), [
                "{{ ModuleName }}" => $this->getModuleName(),
                "{{ module_name }}" => $this->getSnakeCaseName(2),
                "{{ module_name_access }}" => Permission::key($this->getSnakeCaseName(2), Permission::access),
            ]);

            $this->makeFile($path, $this->getContent($path, ["/*hexide_admin_stub*/" => $content]), true);

            $this->warn('Menu item appended.');
        } else {
            $this->error('Can`t add menu item to file config/adminlte.php - file does not exist');
        }
    }

    private function appendMenuItemTranslations()
    {
        $this->info('Appending menu item translations...');

        $locales = $this->config->get('hexide_admin.locales');
        list($path, $file_name) = $this->config->get('hexide_admin.module_paths.adminlte_menu_translations');

        $path = base_path($path);
        foreach ($locales as $locale) {
            $file_path = $path . "$locale/$file_name";

            if ($this->filesystem->isFile($file_path)) {
                $content = $this->getContent($this->resolveStubPath('menu_item', "menu_locale.stub"), [
                    "{{ module_name }}" => $this->getSnakeCaseName(2),
                    "{{ ModuleName }}" => $this->getModuleName(2),
                ]);

                $this->makeFile($file_path, $this->getContent($file_path, ["/*hexide_admin_stub*/" => $content]), true);
            } else {
                $this->error('can`t append menu translations for locale ' . $locale);
            }
        }

        $this->warn('Menu item translations appended');
    }

    private function appendTranslations()
    {
        $this->info('Appending model translations...');

        $locales = $this->config->get('hexide_admin.locales');
        list($path, $file_name) = $this->config->get('hexide_admin.module_paths.lang');

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
                $this->error('can`t append model translations for locale ' . $locale);
            }
        }

        $this->warn('Model translations appended.');
    }

    private function createViews()
    {
        $this->info('Creating views...');

        $dir_path = base_path($this->config->get('hexide_admin.module_paths.views')) . $this->getSnakeCaseName(2);
        $this->makeDir($dir_path);
        $this->makeDir($dir_path . '/tabs');
        $this->makeDir($dir_path . '/partials');

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

            $this->makeFile($dir_path . '/' . $name, $content);
        }

        $this->warn('Views created.');
    }

    //--------------------------------------------------------

    protected function getModuleName($plural = 1): string
    {
        return Str::plural($this->module_name, $plural);
    }

    protected function getSnakeCaseName($plural = 1): string
    {
        return Str::plural(Str::snake($this->getModuleName()), $plural);
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
        $stub = trim($stub, '/');

        return file_exists($customPath = $this->laravel->basePath('stubs/hexide_admin/' . $stub))
            ? $customPath
            : __DIR__ . "/../stubs/" . trim(Arr::get($this->stub_paths, $type), '/') . "/$stub";
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
        if ($this->with_interact && !$force && $this->filesystem->exists($file)) {
            $force = $this->confirm('Ви дійсно хочете переписати цей існуючий файл?', false);
        }

        if (!$this->filesystem->exists($file) || $force) {
            return $this->filesystem->put($file, $content);
        }

        return false;
    }

    /**
     * Get content from file and process replaces
     *
     * @param string $content file path or existed content
     * @param array $replacements
     * @return array|string|string[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
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
            ['name', InputArgument::OPTIONAL, 'Module name in title camel cased type ex. ModuleName'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['service', 's', InputOption::VALUE_NONE, 'Generate with service class'],
            ['translatable', 't', InputOption::VALUE_NONE, 'Generate files with translatable attributes'],
            ['force', 'f', InputOption::VALUE_OPTIONAL, 'Overwrite all existing module files or for only defined types of files (controller,model,service,views)', 'all'],
        ];
    }
}
