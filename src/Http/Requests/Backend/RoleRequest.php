<?php

namespace HexideDigital\HexideAdmin\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use HexideDigital\ModelPermissions\Models\Permission;
use HexideDigital\ModelPermissions\Models\Role;
use Illuminate\Support\Facades\Gate;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (isset($this->role)) {
            return Gate::allows(Permission::Update, Role::find($this->role));
        } else {
            return Gate::allows(Permission::Create, Role::class);
        }
    }

    public function rules(): array
    {
        $modelId = $this->role ?? '';

        return [
            'title' => 'required|min:3|max:100|unique:roles,title,'.$modelId.',id',
            'admin_access' => 'required|bool',
            'permissions' => 'nullable|array',
        ];
    }
}
