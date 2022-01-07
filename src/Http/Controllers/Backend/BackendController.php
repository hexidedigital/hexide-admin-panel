<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

// use HexideDigital\AdminConfigurations\Models\AdminConfiguration;
use HexideDigital\HexideAdmin\Classes\Notifications\NotificationInterface;
use HexideDigital\HexideAdmin\Classes\SecureActions;
use HexideDigital\HexideAdmin\Http\ActionNames;
use HexideDigital\HexideAdmin\Http\Controllers\BaseController;
use HexideDigital\HexideAdmin\Http\Requests\Backend\AjaxFieldRequest;
use HexideDigital\HexideAdmin\Http\ViewNames;
use HexideDigital\HexideAdmin\Services\BackendService;
use HexideDigital\HexideAdmin\Services\ServiceInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use View;

abstract class BackendController extends BaseController
{
    private const Actions = [
        'index', 'show', 'create', 'store', 'edit', 'update', 'destroy',
        'restore', 'forceDelete',
        'ajaxFieldChange',
    ];

    private const DatabaseAction = [
        'store'       => ActionNames::Create,
        'update'      => ActionNames::Edit,
        'destroy'     => ActionNames::Delete,
        'restore'     => ActionNames::Restore,
        'forceDelete' => ActionNames::ForceDelete,
    ];

    private ?Model $model = null;
    private ?string $module = null;
    private ?string $modelClass = null;

    private NotificationInterface $notificator;
    private bool $showErrorNotification = true;

    private ServiceInterface $service;
    private ?string $serviceClass = null;

    private FormRequest $formRequest;
    private ?string $formRequestClassName = null;

    protected SecureActions $secureActions;

    public function __construct()
    {
        parent::__construct();

        $this->secureActions = new SecureActions();

        $this->setNotifier(app(NotificationInterface::class));

        $this->dataUrlParams([]);

//        if (!(AdminConfiguration::where('key', 'show_debug_footer')->first()->status ?? true)) {
//            \Debugbar::disable();
//        }
    }


    /* ------------ Model and module ------------ */

    protected function setModule(string $modelClassName): void
    {
        if (!class_exists($modelClassName)) {
            /* Will throw exception like `Class not found` */
            new $modelClassName;
        }

        $this->setModelClassName($modelClassName);
        $this->setModuleName();
        $this->setServiceClassName();
        $this->setFromRequestClassName();
    }

    protected function setModelClassName(string $modelClassName): void
    {
        $this->modelClass = $modelClassName;
    }

    protected function getModelClassName(): string
    {
        return $this->modelClass ?: config('hexide-admin.namespaces.model') . str_singular(Str::studly($this->getModuleName()));
    }

    protected function setModuleName(string $name = null)
    {
        if (empty($name)) {
            $name = $this->getModelObject()->getTable();
        }

        $this->module = $name;

        $this->data('module', $name);

        $this->secureActions->setModuleName($name);
    }

    protected function getModuleName(): ?string
    {
        return $this->module;
    }

    protected function getModel(): ?Model
    {
        return $this->model;
    }

    protected function setModel(?Model $model): void
    {
        $this->model = $model;
    }

    protected function getModelObject(): Model
    {
        $class = $this->getModelClassName();

        return new $class;
    }

    protected function setServiceClassName(string $serviceClassName = null)
    {
        if (is_null($serviceClassName)) {
            $serviceClassName = $this->getServiceClassName();
        }

        if (!class_exists($serviceClassName)) {
            $serviceClassName = BackendService::class;
        }

        $this->serviceClass = $serviceClassName;
    }

    protected function getServiceClassName(): string
    {
        return $this->serviceClass ?: config('hexide-admin.namespaces.service') . str_singular(Str::studly($this->getModuleName())) . 'Service';
    }

    public function setService(ServiceInterface $service)
    {
        $this->service = $service;
    }

    protected function getService(): ?ServiceInterface
    {
        if (isset($this->service)) {
            return $this->service;
        }

        $class = $this->getServiceClassName();

        return new $class;
    }

    protected function getModelFromRoute(Request $request, string $action = null): ?Model
    {
        if ($this->modelUsesSoftDeletesTrait()) {
            return $this->getModelObject()::withTrashed()->find($request->route(str_singular($this->getModuleName())));
        }

        return $this->getModelObject()::findOrFail($request->route(str_singular($this->getModuleName())));
    }

    protected function setFromRequestClassName(string $requestClassName = null)
    {
        if (is_null($requestClassName)) {
            $requestClassName = $this->getFormRequestClassName();
        }

        if (!class_exists($requestClassName)) {
            $requestClassName = Request::class; // laravel base request
        }

        $this->formRequestClassName = $requestClassName;
    }

    protected function getFormRequestClassName(string $action = null): string
    {
        return $this->formRequestClassName ?: config('hexide-admin.namespaces.request') . str_singular(Str::studly($this->getModuleName())) . 'Request';
    }

    protected function getFormRequest(string $action = null): FormRequest
    {
        return App::make($this->getFormRequestClassName($action));
    }

    /* ------------ Backend actions ------------ */

    public function indexAction()
    {
        return $this->render();
    }

    public function showAction(Request $request)
    {
        $this->dataModel($this->getModelFromRoute($request, ActionNames::Show));

        return $this->render(ViewNames::Show);
    }

    public function createAction()
    {
        return $this->render(ViewNames::Create);
    }

    public function editAction(Request $request)
    {
        $this->dataModel($this->getModelFromRoute($request, ActionNames::Edit));

        return $this->render(ViewNames::Edit);
    }

    /** @throws \Throwable */
    public function storeAction(Request $request): RedirectResponse
    {
        $service = $this->getService();

        $model = $service->handleRequest($request, $this->getModelObject());

        return $this->nextAction($model);
    }

    /** @throws \Throwable */
    public function updateAction(Request $request): RedirectResponse
    {
        $service = $this->getService();

        $model = $service->handleRequest(
            $this->getFormRequest(ActionNames::Edit) ?: $request,
            $this->getModelFromRoute($request, ActionNames::Edit) ?: $this->getModelObject()
        );

        return $this->nextAction($model);
    }

    public function destroyAction(Request $request): RedirectResponse
    {
        $model = $this->getModelFromRoute($request, ActionNames::Delete);

        if (!$model->delete()) {
            throw new \Exception('Model not deleted');
        };

        return back();
    }

    public function restoreAction(Request $request): RedirectResponse
    {
        if (!$this->modelUsesSoftDeletesTrait()) {
            throw new \Exception('Model class not uses SoftDeletes trait');
        }

        $model = $this->getModelFromRoute($request, ActionNames::Restore);

        if (!$model->restore()) {
            throw new \Exception('Model not restored');
        };

        return back();
    }

    public function forceDeleteAction(Request $request): RedirectResponse
    {
        if (!$this->modelUsesSoftDeletesTrait()) {
            throw new \Exception('Model class not uses SoftDeletes trait');
        }

        $model = $this->getModelFromRoute($request, ActionNames::ForceDelete);

        if (!$model->forceDelete()) {
            throw new \Exception('Model not permanently deleted');
        };

        return back();
    }

    private function modelUsesSoftDeletesTrait(): bool
    {
        return in_array(SoftDeletes::class, class_uses($this->modelClass));
    }


    /* ------------ Ajax field action ------------ */

    /**
     * change field = $field of record with id = $id
     * url for controller: (POST) module_name/ajax_field/{id}
     *
     * @param AjaxFieldRequest $request
     *
     * @return JsonResponse
     */
    public function ajaxFieldChangeAction(AjaxFieldRequest $request): JsonResponse
    {
        $model = $this->getModelObject()::find($request->get('id'));

        if ($model) {
            $field = $request->get('field');

            $model->{$field} = $request->get('value');

            if ($model->save()) {
                return response()->json(['message' => __('hexide-admin::messages.success.action'),]);
            }
        }

        return response()->json(['message' => __('hexide-admin::messages.error.action'),], 422);
    }


    /* ------------ Next action ------------ */

    protected function getActionsForView(): array
    {
        return [
            'default' => [
                'index' => __('next_action.index'),
            ],
            'menu'    => [
                'edit'   => __('next_action.edit'),
                'create' => __('next_action.create'),
                'show'   => __('next_action.show'),
            ],
        ];
    }

    protected function nextAction(Model $model = null, array $params = []): RedirectResponse
    {
        $nextAction = request('next_action', 'index');
        $module = $this->getModuleName();

        if (in_array($nextAction, ['edit', 'show'])) {
            $params = array_merge([str_singular($module) => $model], $params);
        }

        return redirect()->route("admin.$module.$nextAction", $params);
    }


    /* ------------ View data ------------ */

    protected function dataModel(?Model $model)
    {
        $this->setModel($model);

        $this->data('model', $model);
    }

    protected function dataUrlParams(array $data)
    {
        $this->data('url_params', $data);
    }


    /* ------------ Secure actions ------------ */

    /**
     * @param string $action
     *
     * @return bool|JsonResponse|RedirectResponse|SymfonyResponse
     */
    protected function protectAction(string $action)
    {
        if (!$this->secureActions->check($action)) {
            if (request()->ajax()) {
                return response()
                    ->json(['message' => __('api_labels.forbidden'), 'type' => 'error'])
                    ->setStatusCode(SymfonyResponse::HTTP_FORBIDDEN);
            } else {
                if ($action != 'index') {
                    return $this->redirect();
                } else {
                    return redirect()->route('admin.home');
                }
            }
        }

        return true;
    }

    protected function setResourceAccessMap(array $merge = []): void
    {
        $this->secureActions->setResourceAccessMap();
        $this->mergeAccessMap($merge);
    }

    protected function setFullAccessMap(array $merge = []): void
    {
        $this->secureActions->setFullAccessMap();
        $this->mergeAccessMap($merge);
    }

    /**
     * @param array<string, string|bool|null> $array
     *
     * @return void
     */
    protected function mergeAccessMap(array $array = []): void
    {
        $this->secureActions->merge($array);
    }


    /* ------------ Notifications ------------ */

    protected function hideErrorNotifications()
    {
        $this->showErrorNotification = false;
    }

    protected function showErrorNotification()
    {
        $this->showErrorNotification = true;
    }

    protected function setNotifier(NotificationInterface $notification): void
    {
        $this->notificator = $notification;
    }

    protected function notifyIfExistsErrors(string $action = '', string $message = ''): void
    {
        if (!empty(request()->old()) && $this->showErrorNotification) {
            $this->notify($action, $message, 'error');
        }
    }

    protected function notify(string $action = '', string $message = null, string $type = 'success', string $title = '', array $options = []): self
    {
        if (!ActionNames::isAllowed($action)) {
            $action = ActionNames::Action;
        }

        if (empty($title)) {
            $title = __("hexide-admin::messages.$type.title");
        }

        if (empty($message)) {
            if (in_array($type, ['error', 'success'])) {
                $message = __("hexide-admin::messages.$type.$action",
                    ['model' => trans_choice("models.{$this->getModuleName()}.name", 1)]
                );
            }
        }

        $this->notificator->notify($message, $type, $title, $options);

        return $this;
    }


    /* ------------ Breadcrumbs ------------ */

    protected function createBreadcrumb(?string $method)
    {
        if (isset($method) && $this->canAddToBreadcrumbs()) {
            $module = $this->getModuleName();

            if ($method == $module) {
                $this->addToBreadcrumbs(
                    trans_choice("models.$module.name", 2),
                    route("admin.$module.index")
                );
            } else if (!empty($method)) {
                $this->addToBreadcrumbs(
                    __("models.$method"),
                    route("admin.$module.$method", $this->model ?? '')
                );
            }
        }
    }


    /* ------------ Controller methods ------------ */

    public function callAction($action, $parameters)
    {
        $this->createBreadcrumb($this->getModuleName());

        $result = $this->protectAction($action);

        if ($result !== true) {
            $message = __('hexide-admin::messages.forbidden', ['key' => $this->module . '.' . $action]);
            $this->notify(ActionNames::Action, $message, 'error');

            return $result;
        }

        return $this->getActionResult($action, $parameters);
    }

    protected function getActionResult(string $action, $parameters)
    {
        if (in_array($action, self::Actions)) {
            if ($this->isDatabaseAction($action)) {
                return $this->dbTransactionAction($action);
            }

            if (method_exists($this, $action)) {
                return parent::callAction($action, $parameters);
            }

            return App::call([$this, $action . 'Action']);
        }

        return parent::callAction($action, $parameters);
    }

    private function dbTransactionAction(string $action)
    {
        DB::beginTransaction();

        try {
            if (!method_exists($this, $action)) {
                $result = App::call([$this, $action . 'Action']);
            } else {
                $result = App::call([$this, $action]);
            }

            $this->notify(self::DatabaseAction[$action]);

            DB::commit();

            return $result;
        } catch (\Throwable $e) {
            $this
                ->notify(self::DatabaseAction[$action], null, 'error')
                ->notify(self::DatabaseAction[$action], $e->getMessage(), 'error');

            DB::rollBack();

            return back();
        }
    }

    private function isDatabaseAction(string $action): bool
    {
        return in_array($action, array_keys(self::DatabaseAction));
    }

    /**
     * @param string|null $view View type or View path
     * @param array $data
     * @param string|null $forceActionType
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory|Application
     */
    protected function render(?string $view = null, array $data = [], string $forceActionType = null)
    {
        if (empty($view)) {
            $view = ViewNames::Index;
        }

        if (in_array($forceActionType, [ViewNames::Edit, ViewNames::Create]) ||
            in_array($view, [ViewNames::Edit, ViewNames::Create])) {

            $forceActionType = $forceActionType ?? $view;

            $this->notifyIfExistsErrors($forceActionType);

            $this->data('layout_type', $forceActionType);
        } else {
            $this->notifyIfExistsErrors();
        }

        $module = $this->getModuleName();
        $view = View::exists("admin.view.$module.$view") ? "admin.view.$module.$view" : $view;

        $this->createBreadcrumb($forceActionType ?: array_last(explode('.', $view)));

        $this->data('next_actions', $this->getActionsForView());

        return parent::render($view, $data);
    }

    protected function redirect(string $action = null, array $params = []): RedirectResponse
    {
        if (empty($action)) {
            $action = ActionNames::Index;
        }

        $module = $this->getModuleName();
        $route = Route::has("admin.$module.$action") ? "admin.$module.$action" : $action;

        return redirect()->route($route, $params);
    }
}
