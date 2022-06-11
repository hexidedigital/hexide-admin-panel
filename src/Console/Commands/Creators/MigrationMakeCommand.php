<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Console\Commands\Creators;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MigrationMakeCommand extends GeneratorCommand
{
    protected $name = 'hd-admin:make:migration';
    protected $description = 'Create a new Migration file only for creating new table';

    protected string $type = 'Migration';
    protected string $classSuffix = '';

    protected function qualifyClass(string $name): string
    {
        return Str::camel($name);
    }

    protected function getPath(string $name): string
    {
        return database_path('migrations/' . date('Y_m_d_His') . '_' . $this->migrationName($name) . '.php');
    }

    protected function getStub(): string
    {
        $type = $this->isTranslatable() ? '.translation' : '';

        return $this->resolveStubPath("database/migration.create$type.stub");
    }

    private function migrationName(string $name): string
    {
        return (string)\Str::of($name)
            ->ucfirst()
            ->plural()
            ->prepend('Create')
            ->append('Table')
            ->snake();
    }

    protected function getOptions(): array
    {
        return [
            new InputOption('translatable', 't', InputOption::VALUE_NONE, 'Generate files with translatable attributes'),
            new InputOption('model', 'm', InputOption::VALUE_REQUIRED, 'For model'),
            new InputOption('force', null, InputOption::VALUE_NONE, 'Create the class even if the table already exists'),
        ];
    }
}
