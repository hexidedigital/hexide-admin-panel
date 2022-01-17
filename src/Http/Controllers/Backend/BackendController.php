<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use Eloquent;
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

abstract class BackendController extends BaseController
{
    private const Actions = [
        'index', 'show', 'create', 'store', 'edit', 'update', 'destroy',
        'restore', 'forceDelete',
        'ajaxFieldChange',
    ];

    private const DatabaseAction = [
        'store' => ActionNames::Create,
        'update' => ActionNames::Edit,
        'destroy' => ActionNames::Delete,
        'restore' => ActionNames::Restore,
        'forceDelete' => ActionNames::ForceDelete,
    ];

    private ?Model $model = null;
    private ?string $module = null;
    /** @var class-string<Model|Eloquent|SoftDeletes>|string|null */
    private ?string $modelClass = null;

    private NotificationInterface $notificator;
    private bool $showErrorNotification = true;

    private ServiceInterface $service;
    /** @var class-string<ServiceInterface|BackendService>|string|null */
    private ?string $serviceClass = null;

    /** @var class-string<FormRequest|Request>|string|null */
    private ?string $formRequestClassName = null;

    protected SecureActions $secureActions;

    public function __construct()
    {
        parent::__construct();

        $this->secureActions = App::get(SecureActions::class);

        $this->setNotifier(app(NotificationInterface::class));

        $this->dataUrlParams([]);

        if (!config('hexide-admin.configurations.show_debug_footer_admin', true)) {
            \Debugbar::disable();
        }
    }


    /* ------------ Backend actions ------------ */

    public function indexAction()
    {
        return $this->render();
    }

    public function showAction(Request $request)
    {
        $model = $this->getModelFromRoute($request, ActionNames::Show);

        $this->secureActions->checkWithAbort(ActionNames::Show, $model);

        $this->dataModel($model);

        return $this->render(ViewNames::Show);
    }

    public function createAction()
    {
        return $this->render(ViewNames::Create);
    }

    public function editAction(Request $request)
    {
        $model = $this->getModelFromRoute($request, ActionNames::Edit);

        $this->secureActions->checkWithAbort(ActionNames::Edit, $model);

        $this->dataModel($model);

        return $this->render(ViewNames::Edit);
    }

    public function storeAction(Request $request): RedirectResponse
    {
        $this->secureActions->checkWithAbort(ActionNames::Index, $this->getModelClassName());

        $service = $this->getService();

        $model = $service->handleRequest($request, $this->getModelObject());

        return $this->nextAction($model);
    }

    public function updateAction(Request $request): RedirectResponse
    {
        $model = $this->getModelFromRoute($request, ActionNames::Edit) ?: $this->getModelObject();

        $this->secureActions->checkWithAbort(ActionNames::Edit, $model);

        $service = $this->getService();

        $model = $service->handleRequest(
            $this->getFormRequest(ActionNames::Edit) ?: $request,
            $model
        );

        return $this->nextAction($model);
    }

    public function destroyAction(Request $request): RedirectResponse
    {
        $model = $this->getModelFromRoute($request, ActionNames::Delete);

        $this->secureActions->checkWithAbort(ActionNames::Delete, $model);

        $service = $this->getService();
        $service->deleteModel($request, $model);

        return back();
    }

    public function restoreAction(Request $request): RedirectResponse
    {
        $model = $this->getModelFromRoute($request, ActionNames::Restore);

        $this->secureActions->checkWithAbort(ActionNames::Restore, $model);

        $service = $this->getService();
        $service->restoreModel($request, $model);

        return back();
    }

    public function forceDeleteAction(Request $request): RedirectResponse
    {
        $model = $this->getModelFromRoute($request, ActionNames::ForceDelete);

        $this->secureActions->checkWithAbort(ActionNames::ForceDelete, $model);

        $service = $this->getService();
        $service->forceDeleteModel($request, $model);

        return back();
    }

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
        $model = $this->getModelObject()::findOrFail($request->get('id'));

        $this->secureActions->checkWithAbort('ajaxFieldChange', $model);

        if ($model) {
            $field = $request->get('field');

            $model->{$field} = $request->get('value');

            if ($model->save()) {
                return response()->json(['message' => trans('hexide-admin::messages.success.action'),]);
            }
        }

        return response()->json(['message' => trans('hexide-admin::messages.error.action'),], 422);
    }


    /** @throws \Throwable */
    protected function getActionResult(string $action, $parameters)
    {
        if (in_array($action, self::Actions)) {
            if ($this->isDatabaseAction($action)) {
                return $this->dbTransactionAction($action, $parameters);
            }

            if (method_exists($this, $action)) {
                return parent::callAction($action, $parameters);
            }

            return App::call([$this, $action . 'Action']);
        }

        return parent::callAction($action, $parameters);
    }

    /** @throws \Throwable */
    protected function dbTransactionAction(string $action, $parameters)
    {
        DB::beginTransaction();

        try {
            if (method_exists($this, $action)) {
                $result = parent::callAction($action, $parameters);
            } else {
                $result = App::call([$this, $action . 'Action']);
            }

            $this->notify(self::DatabaseAction[$action]);

            DB::commit();

            return $result;
        } catch (\Throwable $e) {
            $this
                ->notify(self::DatabaseAction[$action], null, 'error')
                ->notify(self::DatabaseAction[$action],
                    class_basename($e) . " -- {$e->getFile()}: {$e->getLine()} ",
                    'error',
                    class_basename($e) . $e->getMessage());

            DB::rollBack();
        }

        return back();
    }

    /* ------------ Model and module ------------ */

    protected function initModule(string $modelClassName): void
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

    /** @return class-string<Model|Eloquent>|string */
    protected function getModelClassName(): string
    {
        return $this->modelClass ?: config('hexide-admin.namespaces.model') . '\\' . str_singular(Str::studly($this->getModuleName()));
    }

    protected function setModuleName(string $name = null)
    {
        if (empty($name)) {
            $name = $this->getModelObject()->getTable();
        }

        $this->module = $name;

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

    protected function setModel(Model $model): void
    {
        $this->model = $model;
    }

    /** @return Model|Eloquent|SoftDeletes */
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

    /** @return class-string<ServiceInterface|BackendService>|string */
    protected function getServiceClassName(): string
    {
        return $this->serviceClass ?: config('hexide-admin.namespaces.service') . '\\' . str_singular(Str::studly($this->getModuleName())) . 'Service';
    }

    protected function setService(ServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * @return ServiceInterface|BackendService|null
     */
    protected function getService(): ?ServiceInterface
    {
        if (isset($this->service)) {
            return $this->service;
        }

        $class = $this->getServiceClassName();

        return new $class;
    }

    /**
     * @param Request $request
     * @param string|null $action
     *
     * @return Model|Eloquent|SoftDeletes|null
     */
    protected function getModelFromRoute(Request $request, string $action = null): ?Model
    {
        if (in_array($action, [ActionNames::Index])) {
            return $this->getModelObject();
        }

        $id = $request->route(
            str_singular($this->getModuleName()),
            $request->get('id')
        );

        if (in_array(SoftDeletes::class, class_uses($this->modelClass))) {
            return $this->getModelObject()::withTrashed()->findOrFail($id);
        }

        return $this->getModelObject()::findOrFail($id);
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

    /**
     * @param string|null $action
     *
     * @return class-string<FormRequest|Request>|string
     */
    protected function getFormRequestClassName(string $action = null): string
    {
        return $this->formRequestClassName ?: config('hexide-admin.namespaces.request') . '\\' . str_singular(Str::studly($this->getModuleName())) . 'Request';
    }

    /**
     * @param string|null $action
     *
     * @return FormRequest|Request
     */
    protected function getFormRequest(string $action = null): Request
    {
        return App::make($this->getFormRequestClassName($action));
    }


    /* ------------ Next action ------------ */

    protected function getActionsForView(): array
    {
        return [
            'default' => [
                'index' => trans('next_action.index'),
            ],

            'menu' => [
                'edit' => trans('next_action.edit'),
                'create' => trans('next_action.create'),
                'show' => trans('next_action.show'),
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

    protected function dataModel(Model $model)
    {
        $this->setModel($model);

        $this->data('model', $model);
    }

    /** set url parameters for some links on page */
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
        $type = $this->model;
        if (empty($this->model) && !empty($this->modelClass)) {
            $type = $this->getModelObject();
        }

        if (!$this->secureActions->check($action, $type)) {
            if (request()->ajax()) {
                return response()
                    ->json(['message' => trans('api_labels.forbidden'), 'type' => 'error'])
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
            $title = trans("hexide-admin::messages.$type.title");
        }

        if (empty($message)) {
            if (in_array($type, ['error', 'success'])) {
                $message = trans("hexide-admin::messages.$type.$action",
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
            $this->addToBreadcrumbs(
                $this->getNameForBreadcrumb($method),
                $this->getRouteForBreadcrumb($method),
            );
        }
    }

    protected function getNameForBreadcrumb(string $method): string
    {
        $module = $this->getModuleName();

        if ($method == $module) {
            return trans_choice("models.$module.name", 2);
        }

        return trans("models.$method");
    }

    protected function getRouteForBreadcrumb(string $method): string
    {
        $module = $this->getModuleName();

        if ($method == $module) {
            return route("admin.$module.index");
        }

        return route("admin.$module.$method", $this->model ?? '');
    }


    /* ------------ Controller methods ------------ */

    public function callAction($method, $parameters)
    {
        $this->createBreadcrumb($this->getModuleName());

        $result = $this->protectAction($method);

        if ($result !== true) {
            $message = trans('hexide-admin::messages.forbidden', ['key' => $this->module . '.' . $method]);
            $this->notify(ActionNames::Action, $message, 'error');

            return $result;
        }

        return $this->getActionResult($method, $parameters);
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

        $view = $this->guessViewName($view);

        $this->createBreadcrumb($forceActionType ?: array_last(explode('.', $view)));

        $this->data('next_actions', $this->getActionsForView());
        $this->data('module', $this->getModuleName());
        if (empty($this->model) && !empty($this->modelClass)) {
            $this->dataModel($this->getModelObject());
        }

        return parent::render($view, $data);
    }

    protected function guessViewName(string $view): string
    {
        $module = Str::snake($this->getModuleName());

        if (\View::exists($viewPath = "admin.view.$module.$view")) {
            return $viewPath;
        }

        if (\View::exists($viewPath = "admin.view.$view")) {
            return $viewPath;
        }

        if (\View::exists($viewPath = "hexide-admin::admin.view.$view")) {
            return $viewPath;
        }

        if (\View::exists($viewPath = "admin.$view")) {
            return $viewPath;
        }

        return $view;
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

    private function isDatabaseAction(string $action): bool
    {
        return in_array($action, array_keys(self::DatabaseAction));
    }

}
