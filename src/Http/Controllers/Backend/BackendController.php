<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

// use HexideDigital\AdminConfigurations\Models\AdminConfiguration;
use HexideDigital\FileUploader\Traits\FileUploadingTrait;
use HexideDigital\HexideAdmin\Classes\ActionNames;
use HexideDigital\HexideAdmin\Classes\Notifications\NotificationInterface;
use HexideDigital\HexideAdmin\Classes\SecureActions;
use HexideDigital\HexideAdmin\Classes\ViewNames;
use HexideDigital\HexideAdmin\Http\Controllers\BaseController;
use HexideDigital\HexideAdmin\Http\Requests\Backend\AjaxFieldRequest;
use HexideDigital\HexideAdmin\Services\ServiceInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use View;

abstract class BackendController extends BaseController
{
    use FileUploadingTrait;

    protected const Actions = [
        'index', 'show', 'create', 'store', 'edit', 'update', 'destroy',
        'ajaxFieldChange'
    ];

    private ?string $module = null;
    private ?string $modelClass = null;

    private ?Model $model = null;

    protected SecureActions $secureActions;

    protected ServiceInterface $service;
    protected NotificationInterface $notificator;
    protected ?bool $showErrorNotification = true;


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
    }

    protected function setModelClassName(string $modelClassName): void
    {
        $this->modelClass = $modelClassName;
    }

    protected function getModelClassName(): string
    {
        return $this->modelClass ?: 'App\\Models\\' . str_singular(Str::studly($this->getModuleName()));
    }

    protected function getModelObject(): ?Model
    {
        $class = $this->getModelClassName();

        return new $class;
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

    protected function getModelFromRoute(Request $request)
    {
        return $this->getModelObject()::find($request->route(str_singular($this->getModuleName())));
    }

    /* ------------ Backend actions ------------ */

    public function indexAction()
    {
        return $this->render();
    }

    public function showAction(Request $request)
    {
        $this->dataModel($this->getModelFromRoute($request));

        return $this->render(ViewNames::Show);
    }

    public function createAction()
    {
        return $this->render(ViewNames::Create);
    }

    public function editAction(Request $request)
    {
        $this->dataModel($this->getModelFromRoute($request));

        return $this->render(ViewNames::Edit);
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
                return response()->json(['message' => __('hexide_admin::messages.success.action'),]);
            }
        }

        return response()->json(['message' => __('hexide_admin::messages.error.action'),], 422);
    }


    /* ------------ Next action ------------ */

    protected function getActionsForView(): array
    {
        return [
            'default' => [
                'index' => __('next_action.index'),
            ],
            'menu' => [
                'edit' => __('next_action.edit'),
                'create' => __('next_action.create'),
                'show' => __('next_action.show'),
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
        $this->model = $model;
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

    protected function setResourceAccessMap(): void
    {
        $this->secureActions->setResourceAccessMap();
    }

    protected function setFullAccessMap(): void
    {
        $this->secureActions->setFullAccessMap();
    }

    /**
     * @param array<string, string|bool|null>|null $array
     * @return void
     */
    protected function mergeAccessMap(?array $array): void
    {
        $this->secureActions->merge($array);
    }


    /* ------------ Notifications ------------ */

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

    protected function notify(string $action = '', string $message = null, string $type = 'success', string $title = '', array $options = []): \Yoeunes\Toastr\Toastr
    {
        if (!ActionNames::isAllowed($action)) {
            $action = ActionNames::Action;
        }

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

        return $this->notificator->notify($message, $type, $title, $options);
    }


    /* ------------ Breadcrumbs ------------ */

    protected function addToBreadcrumbs($method)
    {
        if (isset($method) && $this->withBreadcrumbs) {
            $module = $this->getModuleName();

            if ($method == $module) {
                $this->breadcrumbs->push(
                    trans_choice("models.$module.name", 2),
                    route("admin.$module.index")
                );
            } else if (!empty($method)) {
                $this->breadcrumbs->push(
                    __("models.$method"),
                    route("admin.$module.$method", $this->model ?? '')
                );
            }
        }
    }


    /* ------------ Controller methods ------------ */

    public function callAction($action, $parameters)
    {
        $this->addToBreadCrumbs($this->getModuleName());

        $result = $this->protectAction($action);

        if ($result !== true) {
            $message = __('hexide_admin::messages.forbidden', ['key' => $this->module . '.' . $action]);
            $this->notify(ActionNames::Action, $message, 'error');

            return $result;
        }

        if (in_array($action, self::Actions) && !method_exists($this, $action)) {
            $action = $action . 'Action';

            return \App::call([$this, $action]);
        }

        return parent::callAction($action, $parameters);
    }

    /**
     * @param string|null $view View type or View path
     * @param array $data
     * @param string|null $force_type
     * @return Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function render(?string $view = null, array $data = [], string $force_type = null)
    {
        if (empty($view)) $view = ViewNames::Index;

        if (in_array($force_type, [ViewNames::Edit, ViewNames::Create]) ||
            in_array($view, [ViewNames::Edit, ViewNames::Create])) {

            $force_type = $force_type ?? $view;

            $this->notifyIfExistsErrors($force_type);

            $this->data('layout_type', $force_type);
        } else {
            $this->notifyIfExistsErrors();
        }

        $module = $this->getModuleName();
        $view = View::exists("admin.view.$module.$view") ? "admin.view.$module.$view" : $view;

        $this->addToBreadcrumbs($force_type ?: array_last(explode('.', $view)));

        $this->data('next_actions', $this->getActionsForView());

        return parent::render($view, $data);
    }

    protected function redirect(string $action = null, array $params = []): RedirectResponse
    {
        if (empty($action)) $action = ActionNames::Index;

        $module = $this->getModuleName();
        $route = Route::has("admin.$module.$action") ? "admin.$module.$action" : $action;

        return redirect()->route($route, $params);
    }
}
