<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Console\Commands;

use HexideDigital\ModelPermissions\Models\Permission;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleMakeCommand extends BaseCommand
{
    protected $name = 'hd-admin:make:module';
    protected $description = 'Generate module files such as model, controllers, views etc.';

    /** @var array<string, string> */
    protected array $stubPaths = [
        'views' => 'views',
        'lang' => 'lang',
        'routes' => 'routes',
        'menu_item' => 'menu_item',
    ];

    protected Filesystem $filesystem;

    /** Camel cased ModuleName */
    protected Stringable $moduleName;
    protected bool $translatable;

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }


    public function handle()
    {
        $this->info('Start: creating module files...');

        $this->setModuleName();
        $this->setTranslatableOption();

        $this->createFiles();
        $this->prepareResources();

        $this->info('Finish: module files are created.');
    }

    private function setModuleName()
    {
        $name = Str::of($this->argument('name'))->studly()->singular();

        $name = $this->ask('Enter the module name', $name);

        $this->info('Create module ' . $name);

        if (empty($name) || preg_match('([^A-Za-z0-9_/\\\\])', $name)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $this->moduleName = Str::of($name);
    }

    private function setTranslatableOption()
    {
        $this->translatable = $this->option('translatable') !== null
            ? $this->option('translatable')
            : $this->confirm('Create a model with translated fields?', $this->option('translatable'));
    }

    //--------------------------------------------------------

    private function createFiles(): void
    {
        $this->info('Creating files...');

        $this->createModels();
        $this->createMigrations();
        $this->createPolicy();
        $this->createRequest();
        $this->createController();
        $this->createLivewire();

        if ($this->option('service')) {
            $this->createService();
        }

        $this->createViews();

        $this->info('Files created.');
    }

    private function createModels(): void
    {
        $this->call('hd-admin:make:model', [
            'name' => $this->getModuleName(),
            '--translatable' => $this->isTranslatable(),
            '--force' => $this->isForced(),
        ]);
    }

    private function createPolicy(): void
    {
        $this->call('hd-admin:make:policy', [
            'name' => $this->getModuleName()->append('Policy'),
            '--model' => $this->getModuleName(),
            '--force' => $this->isForced(),
        ]);
    }

    private function createService(): void
    {
        $this->call('hd-admin:make:service', [
            'name' => $this->getModuleName()->append('Service'),
            '--model' => $this->getModuleName(),
            '--force' => $this->isForced(),
        ]);
    }

    private function createRequest(): void
    {
        $this->call('hd-admin:make:request', [
            'name' => $this->getModuleName()->append('Request'),
            '--translatable' => $this->isTranslatable(),
            '--model' => $this->getModuleName(),
            '--force' => $this->isForced(),
        ]);
    }

    private function createController(): void
    {
        $this->call('hd-admin:make:controller', [
            'name' => $this->getModuleName()->append('Controller'),
            '--model' => $this->getModuleName(),
            '--force' => $this->isForced(),
        ]);
    }

    private function createLivewire(): void
    {
        $this->call('hd-admin:make:livewire-table', [
            'name' => $this->getModuleName()->append('Table'),
            '--translatable' => $this->isTranslatable(),
            '--model' => $this->getModuleName(),
            '--force' => $this->isForced(),
        ]);
    }

    private function createMigrations(): void
    {
        $this->call('hd-admin:make:migration', [
            'name' => $this->getModuleName(),
            '--translatable' => $this->isTranslatable(),
            '--model' => $this->getModuleName(),
            '--force' => $this->isForced(),
        ]);
    }

    private function createViews(): void
    {
        $dirPath = base_path(config('hexide-admin.module_paths.views')) . $this->getModuleName(2)->snake();

        $force = $this->isForced();

        if (!$this->makeDir($dirPath, $force)) {
            $this->warn("Directory for model views already exists $dirPath");
        }

        $this->makeDir($dirPath . '/tabs', $force);
        $this->makeDir($dirPath . '/partials', $force);

        $stubs = array_filter([
            'show.stub' => 'show.blade.php',
            '_form.stub' => 'partials/_form.blade.php',
            'tabs/general.stub' => 'tabs/general.blade.php',
            'tabs/locale.stub' => $this->isTranslatable() ? 'tabs/locale.blade.php' : false,
        ]);

        foreach ($stubs as $stub => $name) {
            $content = $this->getContentWithReplace($this->resolveStubPath('views', $stub), [
                '{{ show_locale_tabs }}' => $this->isTranslatable() ? 'true' : 'false',
            ]);

            $this->makeFileOrPutContent($dirPath . '/' . $name, $content, $force);
        }
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
                '{{ use_controller }}' => (string)Str::of($this->getNamespace('controller'))
                    ->start('App\\')
                    ->append('\\' . $this->getModuleName() . 'Controller'),
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
            '{{ module_name_access }}' => Permission::key((string)$this->getModuleName(2)->snake(), Permission::ViewAny),
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
    protected function getModuleName(int $plural = 1): Stringable
    {
        return $plural === 1
            ? $this->moduleName->singular()
            : $this->moduleName->plural();
    }

    protected function getModelNamespace(): string
    {
        return (string)$this->getNamespace('model', 'Models')
            ->start(app()->getNamespace())
            ->finish('\\')
            ->append($this->getModuleName());
    }

    //--------------------------------------------------------

    protected function getNamespace(string $type, string $default = null): Stringable
    {
        return Str::of(config("hexide-admin.namespaces.$type", $default));
    }

    protected function pathFromNamespace(string $namespace): Stringable
    {
        $dirPath = Str::of($namespace)->replace(['App\\', '\\',], ['', DIRECTORY_SEPARATOR,]);

        return Str::of(app_path($dirPath));
    }

    protected function resolveStubPath(string $type, string $stub): string
    {
        $stub = trim(Arr::get($this->stubPaths, $type, ''), '/') . '/' . trim($stub, '/');

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

    protected function isForced(string $type = null): bool
    {
        return (bool)$this->option('force');
    }

    protected function isTranslatable(): bool
    {
        return $this->translatable;
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

        $replacements = collect([
            '{{ model_namespace }}' => $this->getModelNamespace(),

            '{{ ModelName }}' => $this->getModuleName(),
            '{{ ModelNames }}' => $this->getModuleName(2),

            '{{ model-name }}' => $this->getModuleName()->kebab(),
            '{{ model-names }}' => $this->getModuleName(2)->kebab(),

            '{{ model_name }}' => $this->getModuleName()->snake(),
            '{{ model_names }}' => $this->getModuleName(2)->snake(),

            '{{ table }}' => $this->getModuleName(2)->snake(),
            '{{ table_singular }}' => $this->getModuleName()->snake(),

            '{{ parent_model }}' => $this->getModuleName()->snake(),
        ])->merge($replacements);

        return str_replace($replacements->keys()->toArray(), $replacements->values()->toArray(), $content);
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
        $commands = [];
//
//        foreach ([
//                     ModelMakeCommand::class,
//                     ControllerMakeCommand::class,
//                     LivewireTableMakeCommand::class,
//                     PolicyMakeCommand::class,
//                     RequestMakeCommand::class,
//                     ServiceMakeCommand::class,
//                 ] as $class) {
//            /** @var GeneratorCommand $command */
//            $command = app()->make($class);
//
//            $commands[] = new InputOption(\Str::lower($command->getType()), null, InputOption::VALUE_NONE, $command->getDescription());
//        }

        return array_merge($commands, [
            new InputOption('service', 's', InputOption::VALUE_NONE, 'Generate with service class'),
            new InputOption('translatable', 't', InputOption::VALUE_NONE, 'Generate files with translatable attributes'),
            new InputOption('resources', 'r', InputOption::VALUE_NONE, 'Enable appending and modify resource filed (lang,menu,routes,models)'),
            new InputOption('force', null, InputOption::VALUE_NONE, 'Create the classes even if the class already exists'),
        ]);
    }
}
