<?php

namespace HexideDigital\HexideAdmin\Classes;

use Illuminate\Container\Container;

class HexideAdmin
{
    protected Container $container;

    /**
     * @var Breadcrumbs|mixed
     */
    protected $breadcrumbs;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->breadcrumbs = app()->get(Breadcrumbs::class);
    }

    /**
     * @return Breadcrumbs
     */
    public function getBreadcrumbs(): Breadcrumbs
    {
        return $this->breadcrumbs;
    }
}
