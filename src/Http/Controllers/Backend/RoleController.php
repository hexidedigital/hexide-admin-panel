<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use HexideDigital\HexideAdmin\Http\Requests\Backend\RoleRequest;
use HexideDigital\HexideAdmin\Models\Permission;
use HexideDigital\HexideAdmin\Models\Role;
use HexideDigital\ModelPermissions\Models\Permission as HDPermission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends BackendController
{
    protected array $accessMap = [
        'index'             => HDPermission::access,
        'show'              => HDPermission::view,
        'create'            => HDPermission::create,
        'store'             => HDPermission::create,
        'edit'              => HDPermission::edit,
        'update'            => HDPermission::edit,
        'destroy'           => HDPermission::delete,
    ];

    public function __construct()
    {
        parent::__construct('roles');
    }

    /**
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if ($request->get('draw')) {

            $list = Role::with('permissions')
                ->select(['id', 'title']);

            return $this->_datatable($list);
        }

        return $this->render();
    }

    public function show(Role $role)
    {
        $this->data_model($role->load('permissions'));

        return $this->render(self::VIEW_SHOW);
    }

    public function create()
    {
        $this->data([
            'permissions' => Permission::pluck('title', 'id'),
        ]);

        return $this->render(self::VIEW_CREATE);
    }

    public function store(RoleRequest $request): \Illuminate\Http\RedirectResponse
    {
        $role = Role::create($request->only('title', 'admin_access'));
        $role->permissions()->sync($request->input('permissions', []));

        $this->toastr(self::VIEW_CREATE);

        return $this->redirect();
    }

    public function edit(Role $role)
    {
        $this->data([
            'model' => $role->load('permissions'),
            'permissions' => Permission::pluck('title', 'id'),
        ]);

        return $this->render(self::VIEW_EDIT);
    }

    public function update(RoleRequest $request, Role $role): \Illuminate\Http\RedirectResponse
    {
        $role->update($request->only('title', 'admin_access'));
        $role->permissions()->sync($request->input('permissions', []));

        $this->toastr(self::VIEW_EDIT);

        return $this->redirect();
    }

    public function destroy(Role $role): \Illuminate\Http\RedirectResponse
    {
        $role->delete();

        $this->toastr(self::ACTION_DELETE);

        return back();
    }

    /**
     * @throws \Exception
     */
    private function _datatable(Builder $list): \Illuminate\Http\JsonResponse
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
            ->addColumn(
                'permission_id',
                function ($model) {
                    return view('admin.view.permissions.partials.permissions_badges',
                        ['permissions' => $model->permissions])->render();
                }
            )
            ->rawColumns(['actions', 'permission_id'])
            ->make();
    }
}
