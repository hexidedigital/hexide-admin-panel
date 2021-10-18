<?php

namespace HexideDigital\HexideAdmin\Http\Controllers;

use Illuminate\Routing\Controller;

abstract class BaseController extends Controller
{

    /**
     * @var array
     */
    private array $view_data;

    /**
     * @var array
     */
    protected array $locales;

    /**
     * @var array
     */
    protected array $breadcrumbs;
    protected bool $with_breadcrumbs = true;


    public function __construct()
    {
        $this->locales = config('app.locales');
        $this->breadcrumbs = [];
        $this->view_data = [];
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
        $this->data('locales', $this->locales);
        $this->data('breadcrumbs', $this->breadcrumbs);

        $this->data($data);

        return view($view, $this->getViewData());
    }

    /**
     * @param string $name
     * @param string|null $url
     */
    protected function breadcrumbs(string $name, ?string $url = null)
    {
        $this->breadcrumbs[] = ['name' => $name, 'url' => $url];
    }

}
