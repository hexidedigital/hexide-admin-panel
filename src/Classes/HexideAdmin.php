<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Classes;

use Illuminate\Container\Container;

class HexideAdmin
{
    protected Container $container;
    protected Breadcrumbs $breadcrumbs;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->breadcrumbs = \App::make(Breadcrumbs::class);
    }

    public function getBreadcrumbs(): Breadcrumbs
    {
        return $this->breadcrumbs;
    }
}
