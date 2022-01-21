<?php

namespace HexideDigital\HexideAdmin\Http\Controllers;

use HexideDigital\HexideAdmin\Classes\Breadcrumbs;
use HexideDigital\HexideAdmin\Classes\HexideAdmin;
use Illuminate\Routing\Controller;
use View;

abstract class BaseController extends Controller
{
    private array $viewData = [];
    private bool $withBreadcrumbs = true;

    protected array $locales = [];

    protected HexideAdmin $hexideAdmin;
    protected Breadcrumbs $breadcrumbs;

    public function __construct()
    {
        $this->hexideAdmin = app(HexideAdmin::class);
        $this->breadcrumbs = $this->hexideAdmin->getBreadcrumbs();
    }

    /* ------------ Breadcrumbs ------------ */

    protected function withBreadcrumbs()
    {
        $this->withBreadcrumbs = true;
    }

    protected function withoutBreadcrumbs()
    {
        $this->withBreadcrumbs = false;
    }

    protected function canAddToBreadcrumbs(): bool
    {
        return $this->withBreadcrumbs;
    }

    /**
     * @param string $name
     * @param string|null $route
     * @return void
     */
    protected function addToBreadcrumbs(string $name, ?string $route = null)
    {
        if ($this->canAddToBreadcrumbs()) {
            $this->breadcrumbs->push($name, $route);
        }
    }

    /**
     * @param string|array $key
     * @param mixed|null $value
     */
    protected function data($key, $value = null)
    {
        if (!is_array($key)) {
            $key = array($key => $value);
        }

        foreach ($key as $_key => $_data) {
            $this->viewData = array_merge(
                $this->viewData,
                array($_key => $_data)
            );
        }
    }

    protected function getViewData(): array
    {
        return $this->viewData;
    }

    /**
     * @param string|null $view
     * @param array $data
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    protected function render(?string $view = null, array $data = [])
    {
        $this->data('breadcrumbs', $this->breadcrumbs->get());

        $this->data($data);

        View::share($this->getViewData());

        return view($view, $this->getViewData());
    }
}
