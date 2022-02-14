<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Console\Commands\Creators;

use Symfony\Component\Console\Input\InputOption;

class RequestMakeCommand extends GeneratorCommand
{
    protected $name = 'hd-admin:make:request';
    protected $description = 'Create a new Request class';

    protected string $type = 'Request';
    protected string $classSuffix = 'Request';

    protected function getStub(): string
    {
        $type = $this->isTranslatable() ? '.translation' : '';

        return $this->resolveStubPath("http/requests/request.admin$type.stub");
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $this->configNamespace($rootNamespace, 'request', 'Http\\Requests\\Backend');
    }

    protected function getOptions(): array
    {
        return [
            new InputOption('translatable', 't', InputOption::VALUE_NONE, 'Generate files with translatable attributes'),
            new InputOption('force', null, InputOption::VALUE_NONE, 'Create the class even if the request already exists'),
            new InputOption('model', 'm', InputOption::VALUE_REQUIRED, 'For model'),
        ];
    }
}
