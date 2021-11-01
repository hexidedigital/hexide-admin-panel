<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use HexideDigital\HexideAdmin\Http\Requests\Backend\PermissionRequest;
use HexideDigital\HexideAdmin\Models\Permission;
use HexideDigital\ModelPermissions\Models\Permission as HDPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends BackendController
{
    protected $accessMap = [
        'index' => HDPermission::access,
        'show' => HDPermission::view,
        'create' => HDPermission::create,
        'store' => HDPermission::create,
        'edit' => HDPermission::edit,
        'update' => HDPermission::edit,
        'destroy' => HDPermission::delete,
    ];

    public function __construct()
    {
        $module = Str::plural('permission');
        parent::__construct($module);
    }

    /**
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if ($request->get('draw')) {
            $list = Permission::select();

            return $this->_datatable($list);
        }

        return $this->render();
    }

    public function show(Permission $permission): \Illuminate\Http\RedirectResponse
    {
        return $this->redirect(self::VIEW_EDIT, ['permission' => $permission]);
    }

    public function create()
    {
        return $this->render(self::VIEW_CREATE);
    }

    public function store(PermissionRequest $request): \Illuminate\Http\RedirectResponse
    {
        $inputs = $request->except('_token', '_method');

        Permission::create($inputs);

        $this->toastr(self::VIEW_CREATE);

        return $this->redirect();
    }

    public function edit(Permission $permission)
    {
        $this->data_model($permission);

        return $this->render(self::VIEW_EDIT);
    }

    public function update(PermissionRequest $request, Permission $permission): \Illuminate\Http\RedirectResponse
    {
        $inputs = $request->except('_token', '_method');

        $permission->update($inputs);

        $this->toastr(self::VIEW_EDIT);

        return $this->redirect();
    }

    public function destroy(Permission $permission): \Illuminate\Http\RedirectResponse
    {
        $permission->delete();

        $this->toastr(self::ACTION_DELETE);

        return back();
    }

    /**
     * @throws \Exception
     */
    private function _datatable($list): \Illuminate\Http\JsonResponse
    {
        return DataTables::eloquent($list)
            ->filterColumn(
                'actions',
                function ($query, $keyword) {
                    $query->whereRaw($this->getModuleName() . '.id like ?', ['%' . $keyword . '%']);
                }
            )
            ->addColumn(
                'actions',
                function ($model) {
                    return view('admin.partials.control_buttons',
                        ['model' => $model, 'module' => $this->getModuleName()]
                    )->render();
                }
            )
            ->rawColumns(['actions'])
            ->make();
    }
}
