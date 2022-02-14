<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Console\Commands\Creators;

use Symfony\Component\Console\Input\InputOption;

class ModelMakeCommand extends GeneratorCommand
{
    protected $name = 'hd-admin:make:model';
    protected $description = 'Create a new Model class';

    protected string $type = 'Model';
    protected string $classSuffix = '';

    public function handle()
    {
        if (parent::handle() === false || !$this->isTranslatable()) {
            return;
        }

        $this->call('hd-admin:make:model-tr', [
            'name' => $this->getNameInput() . 'Translation',
            '--force' => $this->isForced(),
        ]);
    }

    protected function getStub(): string
    {
        $type = $this->isTranslatable() ? '.with_translation' : '';

        return $this->resolveStubPath("models/model$type.stub");
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $this->configNamespace($rootNamespace, 'model', 'Models');
    }

    protected function getOptions(): array
    {
        return [
            new InputOption('translatable', 't', InputOption::VALUE_NONE, 'Generate files with translatable attributes'),
            new InputOption('force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'),
        ];
    }
}
