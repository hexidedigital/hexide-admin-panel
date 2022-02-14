<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Console\Commands\Creators;

use Symfony\Component\Console\Input\InputOption;

class ModelTranslationMakeCommand extends GeneratorCommand
{
    protected $name = 'hd-admin:make:model-tr';
    protected $description = 'Create a new Model Translation class';

    protected string $type = 'Model Translation';
    protected string $classSuffix = 'Translation';

    protected function getStub(): string
    {
        return $this->resolveStubPath('models/model.translation.stub');
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $this->configNamespace($rootNamespace, 'Models', 'Models');
    }

    protected function getOptions(): array
    {
        return [
            new InputOption('force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'),
        ];
    }
}
