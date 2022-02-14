<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Console\Commands\Creators;

use Symfony\Component\Console\Input\InputOption;

class LivewireTableMakeCommand extends GeneratorCommand
{
    protected $name = 'hd-admin:make:livewire-table';
    protected $description = 'Create a new Livewire Table class';

    protected string $type = 'Livewire Table';
    protected string $classSuffix = 'Table';

    protected function getStub(): string
    {
        return $this->resolveStubPath('http/livewire/table.stub');
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $this->configNamespace($rootNamespace, 'livewire-table', 'Http\\Livewire\\Admin\\Tables');
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
