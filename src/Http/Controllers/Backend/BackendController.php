<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use App\Models\User;
use Gate;
use HexideDigital\AdminConfigurations\Models\AdminConfiguration;
use HexideDigital\FileUploader\Traits\FileUploadingTrait;
use HexideDigital\HexideAdmin\Http\Controllers\BaseController;
use HexideDigital\HexideAdmin\Services\ServiceInterface;
use HexideDigital\ModelPermissions\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use View;

abstract class BackendController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use FileUploadingTrait;

    protected const VIEW_SHOW = 'show';
    protected const VIEW_EDIT = 'edit';
    protected const VIEW_CREATE = 'create';

    protected const ACTION_DEFAULT = 'action';
    protected const ACTION_DELETE = 'delete';

    /* follow locale file in lang/__/messages.php' */
    protected const ACTIONS = [
        self::ACTION_DEFAULT,
        self::VIEW_CREATE,
        self::VIEW_EDIT,
        self::ACTION_DELETE,
    ];

    /**
     * @var string|null
     */
    protected $module;

    /**
     * @var Model|null
     */
    protected $model;

    /**
     * @var User|null
     */
    protected $user;

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
     * @var bool
     */
    protected $show_error_msg = true;

    /**
     * setup module name, app locales
     *
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        parent::__construct();

        $this->setModuleName($name);

        $this->user = request('auth_user');
        $this->data('auth_user', $this->user);

        $this->url_params([]);

        if (!(AdminConfiguration::where('key', 'show_debug_footer')->first()->status ?? true)) {
            \Debugbar::disable();
        }
    }

    /**
     * Call controller with the specified parameters.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return bool|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|object|\Symfony\Component\HttpFoundation\Response
     */
    public function callAction($method, $parameters)
    {
        $permission = array_get($this->accessMap, $method);

        if (!isset($permission)) {
            $permission = array_get($this->accessMap, 'all');
        }

        $res = $this->allowIfCan($permission, $method);

        if ($res !== true) {
            return $res;
        }

        $this->addToBreadCrumbs($this->getModuleName());

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
    protected function render(?string $view = 'index', array $data = [], string $force_type = null)
    {
        if (in_array($force_type, [self::VIEW_EDIT, self::VIEW_CREATE]) ||
            in_array($view, [self::VIEW_EDIT, self::VIEW_CREATE])) {
            $force_type = $force_type ?? $view;
            $this->toastrIfExistsErrors($force_type);
            $this->data('layout_type', $force_type);
        } else {
            $this->toastrIfExistsErrors();
        }

        $module = $this->getModuleName();
        $view = View::exists("admin.view.$module.$view") ? "admin.view.$module.$view" : $view;

        $this->addToBreadcrumbs($force_type ?: array_last(explode('.', $view)));

        return parent::render($view, $data);
    }

    protected function addToBreadcrumbs($method)
    {
        if (isset($method) && $this->with_breadcrumbs) {
            $module = $this->getModuleName();

            if ($method == $module) {
                $this->breadcrumbs->push(
                    trans_choice("models.$module.name", 2),
                    route("admin.$module.index")
                );
            } else if (!empty($method)) {
                $this->breadcrumbs->push(
                    __("models.$module.$method"),
                    route("admin.$module.$method", $this->model ?? '')
                );
            }
        }
    }

    protected function redirect(string $action = 'index', array $params = []): \Illuminate\Http\RedirectResponse
    {
        $route = Route::has('admin.' . $this->getModuleName() . '.' . $action) ?
            'admin.' . $this->getModuleName() . '.' . $action
            : $action;

        return redirect()->route($route, $params);
    }

    protected function toastrIfExistsErrors(string $action = '', string $message = '')
    {
        if (!empty(request()->old()) && $this->show_error_msg) {
            $this->toastr($action, $message, 'error');
        }
    }

    protected function toastr(string $action = '', string $message = null, string $type = 'success', string $title = '', array $options = []): \Yoeunes\Toastr\Toastr
    {
        $action = in_array($action, self::ACTIONS) ? $action : self::ACTION_DEFAULT;

        if (empty($title)) {
            $title = __("hexide_admin::messages.$type.title");
        }

        if (empty($message)) {
            if (in_array($type, ['error', 'success'])) {
                $message = __("hexide_admin::messages.$type.$action",
                    ['model' => trans_choice("models.{$this->getModuleName()}.name", 1)]
                );
            }
        }

        return toastr($message, $type, $title, $options);
    }

    /**
     * @param string|bool $permission
     * @param string $action
     * @return bool|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|object
     */
    private function allowIfCan($permission = false, string $action = 'access')
    {
        if ($permission === true) {
            return true;
        }
        if (empty($permission)) {
            return $this->abort($action);
        }

        $key = Permission::key($this->getModuleName(), $permission);

        if ($key === 'home_access' || Gate::allows($key)) {
            return true;
        }
        if (Gate::allows($permission)) {
            return true;
        }

        return $this->abort($action);
    }

    private function abort($action)
    {
        $message = __('hexide_admin::messages.forbidden', ['key' => $action]);

        if (request()->ajax()) {
            return response()->json(['message' => __('hexide_admin::api_labels.forbidden'), 'type' => 'error'])
                ->setStatusCode(Response::HTTP_FORBIDDEN);
        } else {
            $this->toastr('', $message, 'error');
            toastInfo($message, trans_choice("models." . $this->getModuleName() . ".name", 2));

            return redirect(redirect()->back()->getTargetUrl());
        }
    }
}
