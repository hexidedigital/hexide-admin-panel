<?php

namespace HexideDigital\HexideAdmin\Http\Controllers;

use HexideDigital\HexideAdmin\Classes\Breadcrumbs;
use HexideDigital\HexideAdmin\Classes\HexideAdmin;
use Illuminate\Routing\Controller;
use View;

abstract class BaseController extends Controller
{
    /**
     * @var array
     */
    private $view_data = [];

    /**
     * @var array
     */
    protected $locales = [];

    /**
     * @var bool
     */
    protected $with_breadcrumbs = true;

    /**
     * @var HexideAdmin|mixed
     */
    protected $hexideAdmin;

    /**
     * @var Breadcrumbs
     */
    protected $breadcrumbs;

    public function __construct()
    {
        $this->hexideAdmin = app()->get(HexideAdmin::class);
        $this->breadcrumbs = $this->hexideAdmin->getBreadcrumbs();
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
            $this->view_data = array_merge(
                $this->view_data,
                array($_key => $_data)
            );
        }
    }

    protected function getViewData(): array
    {
        return $this->view_data;
    }

    /**
     * @param string|null $view
     * @param array $data
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function render(?string $view = null, array $data = [])
    {
        $this->data('breadcrumbs', $this->breadcrumbs->get());

        $this->data($data);

        View::share($this->getViewData());
        return view($view, $this->getViewData());
    }

}
