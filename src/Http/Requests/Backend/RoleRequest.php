<?php

namespace HexideDigital\HexideAdmin\Http\Requests\Backend;

use HexideDigital\ModelPermissions\Models\Role;

class RoleRequest extends AdminFormRequest
{
    protected string $modelClass = Role::class;
    protected string $routeKeyName = 'role';

    public function rules(): array
    {
        $modelId = $this->modelId();

        return [
            'title' => 'required|min:3|max:100|unique:roles,title,'.$modelId.',id',
            'admin_access' => 'required|bool',
            'permissions' => 'nullable|array',
        ];
    }
}
