<?php

namespace HexideDigital\HexideAdmin\Services\Backend;

use HexideDigital\HexideAdmin\Services\BackendService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use HexideDigital\ModelPermissions\Models\Role;

class RoleService extends BackendService
{
    /**
     * @param FormRequest $request
     * @param Role $model
     *
     * @return Model|Role
     */
    public function postHandle(Request $request, Model $model): Model
    {
        $roles = $request->input('permissions', []);
        $model->permissions()->sync($roles);

        return $model;
    }
}
