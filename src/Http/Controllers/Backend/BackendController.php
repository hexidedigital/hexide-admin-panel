<?php

declare(strict_types=1);

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
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @method void getFormViewData()
 */
abstract class BackendController extends BaseController
{
    use AuthorizesRequests;

    protected const Actions = [
        'index', 'show', 'create', 'store', 'edit', 'update', 'destroy',
        'restore', 'forceDelete',
        'ajaxFieldChange',
    ];

    protected const DatabaseAction = [
        'store' => ActionNames::Create,
        'update' => ActionNames::Edit,
        'destroy' => ActionNames::Delete,
        'restore' => ActionNames::Restore,
        'forceDelete' => ActionNames::ForceDelete,
    ];

    protected bool $selfDescribedController = false;

    protected ?Model $model = null;
    protected ?string $module = null;
    /** @var class-string<Model|Eloquent|SoftDeletes>|string|null */
    protected ?string $modelClass = null;

    protected NotificationInterface $notificator;
    protected bool $showErrorNotification = true;

    protected ServiceInterface $service;
    /** @var class-string<ServiceInterface|BackendService>|string|null */
    protected ?string $serviceClass = null;

    /** @var class-string<FormRequest|Request>|string|null */
    protected ?string $formRequestClassName = null;

    protected SecureActions $secureActions;
    protected bool $catchExceptions = true;

    public function __construct()
    {
        parent::__construct();

        $this->prepareController();
        $this->bootController();
    }

    /* ------------ Backend actions ------------ */

    public function indexAction()
    {
        return $this->render(ActionNames::Index);
    }

    public function showAction(Request $request)
    {
        $model = $this->getModelFromRoute($request, ActionNames::Show);

        $this->protectAction(ActionNames::Show, $model);

        $this->dataModel($model);

        return $this->render(ViewNames::Show);
    }

    public function createAction()
    {
        $this->protectAction(ActionNames::Create, $this->getModelObject());

        return $this->render(ViewNames::Create);
    }

    public function storeAction(Request $request): RedirectResponse
    {
        $this->protectAction(ActionNames::Create, $this->getModelObject());

        $service = $this->getService();

        $model = $service->handleRequest(
            $this->getFormRequest(ActionNames::Create) ?: $request,
            $this->getModelObject()
        );

        return $this->nextActionRedirect($model);
    }

    public function editAction(Request $request)
    {
        $model = $this->getModelFromRoute($request, ActionNames::Edit);

        $this->protectAction(ActionNames::Edit, $model);

        $this->dataModel($model);

        return $this->render(ViewNames::Edit);
    }

    public function updateAction(Request $request): RedirectResponse
    {
        $model = $this->getModelFromRoute($request, ActionNames::Edit);

        $this->protectAction(ActionNames::Edit, $model);

        $service = $this->getService();

        $model = $service->handleRequest(
            $this->getFormRequest(ActionNames::Edit) ?: $request,
            $model
        );

        return $this->nextActionRedirect($model);
    }

    public function destroyAction(Request $request): RedirectResponse
    {
        $model = $this->getModelFromRoute($request, ActionNames::Delete);

        $this->protectAction(ActionNames::Delete, $model);

        $service = $this->getService();
        $service->deleteModel($request, $model);

        return back();
    }

    public function restoreAction(Request $request): RedirectResponse
    {
        $model = $this->getModelFromRoute($request, ActionNames::Restore);

        $this->protectAction(ActionNames::Restore, $model);

        $service = $this->getService();
        $service->restoreModel($request, $model);

        return back();
    }

    public function forceDeleteAction(Request $request): RedirectResponse
    {
        $model = $this->getModelFromRoute($request, ActionNames::ForceDelete);

        $this->protectAction(ActionNames::ForceDelete, $model);

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

        $field = $request->get('field');

        if (!$model->isFillable($field)) {
            return response()->json(['message' => trans('messages.error.action')], 422);
        }

        if ($model->update([$field => $request->get('value')])) {
            return response()->json(['message' => $this->getNotifyModelMessage('success', ActionNames::Edit)]);
        }

        return response()->json(['message' => $this->getNotifyModelMessage('error', ActionNames::Edit)], 422);
    }

    /** @throws \Throwable */
    protected function dbTransactionAction(string $action, $parameters)
    {
        DB::beginTransaction();

        try {
            if (!method_exists($this, $action)) {
                $result = App::call([$this, $action . 'Action'], $parameters);
            } else {
                $result = parent::callAction($action, $parameters);
            }

            $this->notify(self::DatabaseAction[$action]);

            DB::commit();

            return $result;
        } catch (\Throwable $exception) {
            DB::rollBack();

            if ($exception instanceof ValidationException) {
                throw $exception;
            }

            if ((!$this->catchExceptions && App::hasDebugModeEnabled())
                || \Auth::user()->isRoleSuperAdmin()) {
                throw $exception;
            }

            report($exception);
            $this
                ->notify(self::DatabaseAction[$action], 'See logs to get more details about error', 'error')
                ->notify(self::DatabaseAction[$action], class_basename($exception) . $exception->getMessage(), 'error');
        }

        return redirect()->back();
    }

    /* ------------ Model and module ------------ */

    protected function initModule(string $modelClassName): void
    {
        if (!class_exists($modelClassName) && $this->selfDescribedController) {
            /* Will throw exception like `Class not found` */
            new $modelClassName;
        }

        $this->setModelClassName($modelClassName);
        $this->setModuleName();
        $this->setServiceClassName();
        $this->setService($this->getService());
        $this->setFromRequestClassName();
    }

    protected function resolveNamespace(string $type, string $className, string $suffix = null): string
    {
        return (string)Str::of(config("hexide-admin.namespaces.$type"))
            ->start('App\\')
            ->finish('\\')
            ->append(Str::of($className)->studly()->singular() . $suffix);
    }

    protected function setModelClassName(string $modelClassName): void
    {
        $this->modelClass = $modelClassName;
    }

    /** @return class-string<Model|Eloquent>|string|null */
    protected function getModelClassName(): ?string
    {
        return $this->modelClass ?: $this->resolveNamespace('model', $this->getModuleName());
    }

    protected function setModuleName(string $name = null): void
    {
        if (empty($name)) {
            $name = module_name_from_model($this->getModelObject());
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
        return App::get($this->getModelClassName());
    }

    protected function setServiceClassName(string $serviceClassName = null): void
    {
        $this->serviceClass = $this->resolveServiceClassName($serviceClassName);
    }

    protected function resolveServiceClassName(?string $serviceClassName): ?string
    {
        if (is_null($serviceClassName)) {
            $serviceClassName = $this->resolveNamespace('service', $this->getModuleName(), 'Service');
        }

        if (is_null($serviceClassName) || !class_exists($serviceClassName)) {
            return BackendService::class;
        }

        return $serviceClassName;
    }

    /** @return class-string<ServiceInterface|BackendService>|string */
    protected function getServiceClassName(): ?string
    {
        return $this->serviceClass;
    }

    protected function setService(ServiceInterface $service): void
    {
        $this->service = $service;
    }

    /** @return ServiceInterface|BackendService|null */
    protected function getService(): ?ServiceInterface
    {
        if (isset($this->service)) {
            return $this->service;
        }

        $class = $this->getServiceClassName();

        return App::get($class);
    }

    /** @return ServiceInterface|BackendService|null */
    protected function getBackendService(): ?ServiceInterface
    {
        return App::get(BackendService::class);
    }

    /**
     * @param Request|null $request
     * @param string|null $action
     *
     * @return Model|Eloquent|SoftDeletes|null
     */
    protected function getModelFromRoute(Request $request = null, string $action = null): ?Model
    {
        /** @var Request $request */
        $request = $request ?: request();

        if (in_array($action, $this->resourceMethodsWithoutModels())) {
            return $this->getModelObject();
        }

        $id = $request->route(
            str_singular($this->getModuleName()),
            $request->get('id')
        );

        if ($id instanceof Model) {
            return $id;
        }

        if (in_array(SoftDeletes::class, class_uses($this->modelClass))) {
            return $this->getModelObject()::withTrashed()->findOrFail($id);
        }

        return $this->getModelObject()::findOrFail($id);
    }

    protected function setFromRequestClassName(string $requestClassName = null)
    {
        $this->formRequestClassName = $this->resolveFormRequestClassName($requestClassName);
    }

    /**
     * @param string|null $requestClassName
     * @param string|null $action
     * @return string<Request|FormRequest>|null
     */
    protected function resolveFormRequestClassName(?string $requestClassName, ?string $action = null): ?string
    {
        if (is_null($requestClassName)) {
            $requestClassName = $this->resolveNamespace('request', $this->getModuleName(), 'Request');
        }

        if (!class_exists($requestClassName)) {
            return Request::class; // laravel base request
        }

        return $requestClassName;
    }

    /** @return class-string<FormRequest|Request>|string */
    protected function getFormRequestClassName(string $action = null): string
    {
        return $this->formRequestClassName;
    }

    /** @return FormRequest|Request */
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

    protected function nextActionRedirect(Model $model = null, array $params = []): RedirectResponse
    {
        $nextAction = request('next_action', 'index');
        $module = $this->getModuleName();

        if (in_array($nextAction, ['edit', 'show'])) {
            $params[str_singular($module)] = $model;
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
     * @param Model|class-string<Model> $model
     *
     * @return bool|JsonResponse|RedirectResponse|SymfonyResponse
     */
    protected function protectAction(string $action, $model = null)
    {
        if ($this->secureActions->check($action, $model)) {
            return true;
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()
                ->json(['message' => trans('api_labels.forbidden'), 'type' => 'error'])
                ->setStatusCode(403);
        }

        if ($action !== 'index') {
            return $this->redirect();
        }

        return redirect()->route('admin.home');
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

    protected function notify(?string $action = null, ?string $message = null, string $type = 'success', ?string $title = null, array $options = []): self
    {
        if (!ActionNames::isAllowed($action)) {
            $action = ActionNames::Action;
        }

        if (!isset($title)) {
            $title = trans("hexide-admin::messages.$type.title");
        }

        if (!isset($message) && in_array($type, ['error', 'success'])) {
            $message = $this->getNotifyModelMessage($type, $action);
        }

        $this->notificator->notify($message, $type, $title, $options);

        return $this;
    }

    protected function getNotifyModelMessage(string $type = 'success', string $action = 'action'): string
    {
        return __("hexide-admin::messages.$type.$action", [
            'model' => trans_choice("models.{$this->getModuleName()}.name", 1),
        ]);
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

        if (trans()->has($key = "models.$module.$method")) {
            return trans($key);
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

    /* ------------ Hooks ------------ */

    protected function prepareController(): void
    {
        $this->secureActions = App::get(SecureActions::class);

        $this->setNotifier(app(NotificationInterface::class));

        $this->dataUrlParams([]);

        if (!config('hexide-admin.configurations.show_debug_footer_admin', true)) {
//            todo if (class_exists('\\Barryvdh\\Debugbar\\Facades\\Debugbar'))
//            \Debugbar::disable();
        }
    }

    protected function bootController(): void
    {
        // write your code here and not in the constructor
        if ($this->selfDescribedController) {
            $this->setFullAccessMap();

            $this->initModule(
                $this->resolveNamespace('model', str_replace('Controller', '', class_basename($this)))
            );
        }
    }

    protected function beforeCallAnyAction($method, &$parameters): void
    {
        //
    }


    /* ------------ Controller methods ------------ */

    public function callAction($method, $parameters)
    {
        $this->beforeCallAnyAction($method, $parameters);

        $this->createBreadcrumb($this->getModuleName());

        /* // todo move or remove this code
        $result = $this->protectAction($method);

        if ($result !== true) {
            $message = trans('hexide-admin::messages.forbidden', ['key' => $this->module . '.' . $method]);
            $this->notify(ActionNames::Action, $message, 'error');

            return $result;
        }
        */

        if (!in_array($method, self::Actions)) {
            return parent::callAction($method, $parameters);
        }

        if ($this->isDatabaseAction($method)) {
            return $this->dbTransactionAction($method, $parameters);
        }

        if (!method_exists($this, $method)) {
            return App::call([$this, $method . 'Action'], $parameters);
        }

        return parent::callAction($method, $parameters);
    }

    protected function isDatabaseAction(string $action): bool
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
    protected function render(?string $view = ViewNames::Index, array $data = [], string $forceActionType = null)
    {
        $view = $view ?: ViewNames::Index;
        $forceActionType = $forceActionType ?: $view;

        $this->processNotify($view, $forceActionType);

        $this->prepareDataToRender($view, $data);

        $viewName = $this->guessViewName($view);

        $this->createBreadcrumb($forceActionType ?: array_last(explode('.', $viewName)));

        return parent::render($viewName, $data);
    }

    protected function prepareDataToRender(string $view, array &$data): void
    {
        $this->data([
            'next_actions' => $this->getActionsForView(),
            'module' => $this->getModuleName(),
        ]);

        if (empty($this->model) && !empty($this->modelClass)) {
            $this->dataModel($this->getModelObject());
        }

        if (in_array($view, [ViewNames::Create, ViewNames::Edit])) {
            if (method_exists($this, 'getFormViewData')) {
                call_user_func([$this, 'getFormViewData'], compact('view'));
            }
        }
    }

    protected function processNotify(?string $view, ?string $forceActionType): void
    {
        if (in_array($forceActionType, [ViewNames::Edit, ViewNames::Create]) ||
            in_array($view, [ViewNames::Edit, ViewNames::Create])) {
            $this->notifyIfExistsErrors($forceActionType);
            $this->data('layout_type', $forceActionType);
        } else {
            $this->notifyIfExistsErrors();
        }
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
}
