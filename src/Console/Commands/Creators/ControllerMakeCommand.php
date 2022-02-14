<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Console\Commands\Creators;

class ControllerMakeCommand extends GeneratorCommand
{
    protected $name = 'hd-admin:make:controller';
    protected $description = 'Create a new Controller class';

    protected string $type = 'Controller';
    protected string $classSuffix = 'Controller';

    protected function getStub(): string
    {
        return $this->resolveStubPath("http/controllers/controller.admin.stub");
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $this->configNamespace($rootNamespace, 'controller', 'Http\\Controllers\\Backend');
    }
}
