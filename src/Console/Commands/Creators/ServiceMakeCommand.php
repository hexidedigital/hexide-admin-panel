<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Console\Commands\Creators;

class ServiceMakeCommand extends GeneratorCommand
{
    protected $name = 'hd-admin:make:service';
    protected $description = 'Create a new Service class';

    protected string $type = 'Service';
    protected string $classSuffix = 'Service';

    protected function getStub(): string
    {
        return $this->resolveStubPath('service/service.stub');
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $this->configNamespace($rootNamespace, 'service', 'Services\\Backend');
    }
}
