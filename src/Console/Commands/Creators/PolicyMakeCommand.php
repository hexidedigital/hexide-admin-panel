<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Console\Commands\Creators;

class PolicyMakeCommand extends GeneratorCommand
{
    protected $name = 'hd-admin:make:policy';
    protected $description = 'Create a new Policy class';

    protected string $type = 'Policy';
    protected string $classSuffix = 'Policy';

    protected function getStub(): string
    {
        return $this->resolveStubPath('policies/policy.stub');
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $this->configNamespace($rootNamespace, 'policy', 'Policies');
    }
}
