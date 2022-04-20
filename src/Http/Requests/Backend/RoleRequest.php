<?php

namespace HexideDigital\HexideAdmin\Http\Requests\Backend;

use HexideDigital\ModelPermissions\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    use AdminFormRequest;

    protected string $modelClass = Role::class;
    protected string $routeKeyName = 'role';

    public function rules(): array
    {
        $modelId = $this->modelId();

        return [
            'title' => ['required', 'min:3', 'max:100', 'unique:roles,title,'.$modelId.',id'],
            'admin_access' => ['required', 'bool'],
            'permissions' => ['nullable', 'array'],
        ];
    }
}
