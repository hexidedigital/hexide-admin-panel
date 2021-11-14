<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use HexideDigital\AdminConfigurations\Models\AdminConfiguration;
use HexideDigital\FileUploader\Traits\FileUploadingTrait;
use HexideDigital\HexideAdmin\Classes\ActionNames;
use HexideDigital\HexideAdmin\Classes\Notifications\NotificationInterface;
use HexideDigital\HexideAdmin\Classes\ViewNames;
use HexideDigital\HexideAdmin\Http\Controllers\BaseController;
use HexideDigital\HexideAdmin\Http\Traits\CanNotify;
use HexideDigital\HexideAdmin\Http\Traits\ModuleBreadcrumbs;
use HexideDigital\HexideAdmin\Http\Traits\SecureActions;
use HexideDigital\HexideAdmin\Services\ServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use View;

abstract class BackendController extends BaseController
{
    use FileUploadingTrait;
    use SecureActions;
    use ModuleBreadcrumbs;
    use CanNotify;

    /**
     * @var string|null
     */
    protected $module;

    /**
     * @var Model|null
     */
    protected $model;

    /**
     * @var array|bool[]|string[]
     *
     * pair: 'action' => 'permission'
     * <br>
     * if key of action if 'all' = full actions for all actions
     * ['all' => 'access'] or ['all' => true]
     */
    protected $accessMap;

    /**
     * @var ServiceInterface
     */
    protected $service;

    /**
     * @var NotificationInterface
     */
    protected NotificationInterface $notificator;

    /**
     * @var bool
     */
    protected $show_error_notification = true;

    /**
     * setup module name, app locales
     *
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        parent::__construct();

        $this->setNotifier(app(NotificationInterface::class));

        $this->setModuleName($name);

        $this->url_params([]);

        if (!(AdminConfiguration::where('key', 'show_debug_footer')->first()->status ?? true)) {
            \Debugbar::disable();
        }
    }

    public function callAction($method, $parameters)
    {
        $this->addToBreadCrumbs($this->getModuleName());

        $res = $this->protectAction($method);

        if ($res !== true) {
            $message = __('messages.forbidden', ['key' => $method]);
            $this->notify('', $message, 'error');
            return $res;
        }

        return parent::callAction($method, $parameters);
    }

    protected function getModuleName(): ?string
    {
        return trim($this->module);
    }

    protected function setModuleName(?string $name)
    {
        $this->module = trim($name);
        $this->data('module', $name);
    }

    protected function data_model(?Model $model)
    {
        $this->model = $model;
        $this->data('model', $model);
    }

    protected function url_params(array $data)
    {
        $this->data('url_params', $data);
    }

    /**
     * @param string|null $view View type or View path
     * @param array $data
     * @param string|null $force_type
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function render(?string $view = null, array $data = [], string $force_type = null)
    {
        if(empty($view)) $view = ViewNames::INDEX;

        if (in_array($force_type, [ViewNames::EDIT, ViewNames::CREATE]) ||
            in_array($view, [ViewNames::EDIT, ViewNames::CREATE])) {

            $force_type = $force_type ?? $view;

            $this->notifyIfExistsErrors($force_type);

            $this->data('layout_type', $force_type);
        } else {
            $this->notifyIfExistsErrors();
        }

        $module = $this->getModuleName();
        $view = View::exists("admin.view.$module.$view") ? "admin.view.$module.$view" : $view;

        $this->addToBreadcrumbs($force_type ?: array_last(explode('.', $view)));

        return parent::render($view, $data);
    }

    protected function redirect(string $action = null, array $params = []): \Illuminate\Http\RedirectResponse
    {
        if(empty($action)) $action = ActionNames::INDEX;

        $module = $this->getModuleName();
        $route = Route::has("admin.$module.$action") ? "admin.$module.$action" : $action;

        return redirect()->route($route, $params);
    }

}
