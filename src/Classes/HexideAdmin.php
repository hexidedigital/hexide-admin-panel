<?php

namespace HexideDigital\HexideAdmin\Classes;

use Illuminate\Container\Container;

class HexideAdmin
{
    protected Container $container;
    protected Breadcrumbs $breadcrumbs;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->breadcrumbs = app(Breadcrumbs::class);
    }

    public function getBreadcrumbs(): Breadcrumbs
    {
        return $this->breadcrumbs;
    }
}
