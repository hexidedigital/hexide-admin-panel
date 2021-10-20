<?php

namespace HexideDigital\HexideAdmin\Classes;

use Illuminate\Support\Collection;

class Breadcrumbs
{

    protected Collection $breadcrumbs;

    public function __construct()
    {
        $this->breadcrumbs = new Collection();
    }

    public function push(?string $title, ?string $url)
    {
        $this->breadcrumbs->push(['name' => $title, 'url' => $url]);
    }

    public function clear()
    {
        $this->breadcrumbs = new Collection();
    }

    public function get(): Collection
    {
        return $this->breadcrumbs;
    }

}
