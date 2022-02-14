<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Console\Commands\Creators;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

abstract class GeneratorCommand extends Command
{
    protected static string $stubsFolder = __DIR__ . '/../../stubs';

    protected string $type;
    protected string $classSuffix;

    protected Filesystem $files;


    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->files = $filesystem;
    }

    abstract protected function getStub(): string;

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool|void
     *
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $name = $this->qualifyClass($this->getNameInput());

        $path = $this->getPath($name);

        $this->warn($this->getType() . ' creating started...');

        if (!$this->isForced() && $this->alreadyExists($this->getNameInput())) {
            $this->error($this->getType() . ' already exists!');

            return false;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));

        $this->info($this->getType() . ' created successfully.');

        return true;
    }


    /** Get the desired class name from the input. */
    protected function getNameInput(): string
    {
        return trim((string)$this->argument('name'));
    }

    protected function getModelInput(): string
    {
        return trim($this->hasOption('model') ? (string)$this->option('model') : '');
    }

    protected function isForced(): bool
    {
        return $this->hasOption('force') && $this->option('force');
    }

    protected function isTranslatable(): bool
    {
        return $this->hasOption('translatable') && $this->option('translatable');
    }

    /** Parse the class name and format according to the root namespace. */
    protected function qualifyClass(string $name): string
    {
        $name = Str::of($name)
            ->ltrim('\\/')
            ->replace('/', '\\');

        $rootNamespace = $this->rootNamespace();

        if ($name->startsWith($rootNamespace)) {
            return (string)$name;
        }

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')) . '\\' . $name
        );
    }

    /** Qualify the given model class base name. */
    protected function qualifyModel(string $model): string
    {
        $model = Str::of($model)
            ->ltrim('\\/')
            ->replace('/', '\\');

        $rootNamespace = $this->rootNamespace();

        if ($model->startsWith($rootNamespace)) {
            return (string)$model;
        }

        return is_dir(app_path('Models'))
            ? $rootNamespace . 'Models\\' . $model
            : $rootNamespace . $model;
    }

    /** Get the root namespace for the class. */
    protected function rootNamespace(): string
    {
        return $this->laravel->getNamespace();
    }

    /** Get the default namespace for the class. */
    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace;
    }

    protected function getDefaultModel(string $name): string
    {
        return (string)Str::of($name ?: 'Model')
            ->finish($this->classSuffix)
            ->replace($this->classSuffix, '');
    }

    /** Get the destination class path. */
    protected function getPath(string $name): string
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '.php';
    }

    /** Build the class with the given name.
     *
     * @throws FileNotFoundException
     */
    protected function buildClass(string $name): string
    {
        $stub = $this->files->get($this->getStub());

        return $this
            ->replaceNamespace($stub, $name)
            ->replaceModel($stub)
            ->replaceClass($stub, $name);
    }

    protected function replaceModel(string &$stub): self
    {
        $model = $this->getModelInput();

        if (!$model) {
            $model = $this->getDefaultModel($this->getNameInput());
        }

        $model = Str::of($model)->singular();

        $replaces = [
            '{{ model_namespace }}' => $this->qualifyModel((string)$model),

            '{{ ModelName }}' => $model,
            '{{ ModelNames }}' => $model->plural(2),

            '{{ model-name }}' => $model->kebab(),
            '{{ model-names }}' => $model->plural(2)->kebab(),

            '{{ model_name }}' => $model->snake(),
            '{{ model_names }}' => $model->plural(2)->snake(),

            '{{ table }}' => $model->plural(2)->snake(),
            '{{ table_singular }}' => $model->snake(),

            '{{ parent_model }}' => $model->snake(),
        ];

        $stub = str_replace(
            array_keys($replaces),
            array_values($replaces),
            $stub
        );

        return $this;
    }

    protected function replaceNamespace(string &$stub, string $name): self
    {
        $stub = str_replace(
            ['{{ namespace }}', '{{ rootNamespace }}'],
            [$this->getNamespace($name), $this->rootNamespace()],
            $stub
        );

        return $this;
    }

    protected function configNamespace(string $rootNamespace, string $type, string $default = null): string
    {
        return (string)\Str::of(config("hexide-admin.namespaces.$type", $default))->start($rootNamespace . '\\');
    }

    /** Replace the class name for the given stub. */
    protected function replaceClass(string $stub, string $name): string
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);

        return str_replace('{{ class }}', $class, $stub);
    }

    /** Get the full namespace for a given class, without the class name. */
    protected function getNamespace(string $name): string
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    protected function resolveStubPath(string $stubName): string
    {
        return file_exists($customPath = base_path("stubs/hexide-admin/$stubName"))
            ? $customPath
            : static::$stubsFolder . '/' . $stubName;
    }

    /** Get the first view directory path from the application configuration. */
    protected function viewPath(string $path = ''): string
    {
        $views = array_first(config('view.paths'), resource_path('views'));

        return $views . ($path ? '/' . $path : $path);
    }

    /** Determine if the class already exists. */
    protected function alreadyExists(string $rawName): bool
    {
        return $this->files->exists($this->getPath($this->qualifyClass($rawName)));
    }

    /** Build the directory for the class if necessary. */
    protected function makeDirectory(string $path): string
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true, true);
        }

        return $path;
    }

    protected function getArguments(): array
    {
        return [
            new InputArgument('name', InputArgument::REQUIRED, 'Module name in title camel cased type ex. ModuleName'),
        ];
    }

    protected function getOptions(): array
    {
        return [
            new InputOption('force', null, InputOption::VALUE_NONE, 'Create the class even if the ' . strtolower($this->getType()) . ' already exists'),
            new InputOption('model', 'm', InputOption::VALUE_REQUIRED, 'For model'),
        ];
    }
}
