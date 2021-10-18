<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;


use App\Models\User;
use App\Models\Variable;
use Gate;
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
    protected ?string $module;

    /**
     * @var Model|null
     */
    protected ?Model $model;

    /**
     * @var User|null
     */
    protected ?User $user;

    /**
     * @var array|null
     *
     * pair: 'action' => 'permission'
     * <br>
     * if key of action if 'all' = full actions for all actions
     * ['all' => 'access'] or ['all' => true]
     */
    protected array $accessMap = [];

    protected ServiceInterface $service;

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

        if (!(Variable::where('key', 'show_debug_footer')->first()->status ?? true)) {
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

        $name = $this->getModuleName();
        if (isset($name) && $this->with_breadcrumbs) {
            $this->breadcrumbs(
                trans_choice("models.$name.name", 2),
                $name === 'home'
                    ? route("admin.$name")
                    : route("admin.$name.index")
            );
        }

        return parent::callAction($method, $parameters);
    }

    protected function getModuleName(): ?string
    {
        return $this->module;
    }

    protected function setModuleName(?string $name)
    {
        $this->module = $name;
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
        if ($force_type == self::VIEW_EDIT || $view == self::VIEW_EDIT) {
            $this->toastrIfExistsErrors(self::VIEW_EDIT);
            $this->data('layout_type', 'edit');
        } elseif ($force_type == self::VIEW_CREATE || $view == self::VIEW_CREATE) {
            $this->toastrIfExistsErrors(self::VIEW_CREATE);
            $this->data('layout_type', 'add');
        } else {
            $this->toastrIfExistsErrors();
        }

        $view = View::exists('admin.view.' . $this->module . '.' . $view)
            ? 'admin.view.' . $this->module . '.' . $view
            : $view;

        if ($this->with_breadcrumbs) {
            $this->addToBreadcrumbs($force_type ?? array_last(explode('.', $view)));
        }

        return parent::render($view, $data);
    }

    protected function addToBreadcrumbs($method)
    {
        if ($method === 'create') {
            $method = 'add';
        }

        if (!empty($method)) {
            $name = $this->getModuleName();

            if ($method != 'index') {
                $this->breadcrumbs(__("models.$name.$method"),
                    $name === 'home' ? route("admin.$name") : route("admin.$name.index"));
            } else {
                $this->breadcrumbs(__("models.$method"),
                    $name === 'home' ? route("admin.$name") : route("admin.$name.index"));
            }
        }
    }

    protected function redirect(string $action = 'index', array $params = [])
    {
        $route = Route::has('admin.' . $this->getModuleName() . '.' . $action) ?
            'admin.' . $this->getModuleName() . '.' . $action
            : $action;

        return redirect()->route($route, $params);
    }

    protected function toastrIfExistsErrors(string $action = '', string $message = '')
    {
        if (!empty(request()->old())) {
            $this->toastr($action, $message, 'error');
        }
    }

    protected function toastr(string $action = '', string $message = null, string $type = 'success', string $title = '', array $options = [])
    {
        $action = in_array($action, self::ACTIONS) ? $action : self::ACTION_DEFAULT;

        if (empty($title)) {
            $title = __("messages.$type.title");
        }

        if (empty($message)) {
            if (in_array($type, ['error', 'success'])) {
                $message = __("messages.$type.$action",
                    ['model' => trans_choice("models.{$this->getModuleName()}.name", 1)]
                );
            }
        }

        toastr($message, $type, $title, $options);
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
        $message = __('messages.forbidden', ['key' => $action]);

        if (request()->ajax()) {
            return response()->json(['message' => __('api_labels.forbidden'), 'type' => 'error'])
                ->setStatusCode(Response::HTTP_FORBIDDEN);
        } else {
            toastError($message, trans_choice("models." . $this->getModuleName() . ".name", 2));

            return redirect(redirect()->back()->getTargetUrl());
        }
    }
}
