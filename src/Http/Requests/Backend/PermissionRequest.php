<?php

namespace HexideDigital\HexideAdmin\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use HexideDigital\ModelPermissions\Models\Permission;

class PermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (isset($this->permission)) {
            return Gate::allows(Permission::Update, Permission::find($this->permission));
        } else {
            return Gate::allows(Permission::Create, Permission::class);
        }
    }

    public function rules(): array
    {
        $modelId = $this->permission ?? '';

        return [
            'title' => 'required|min:3|max:100|unique:permissions,title,'.$modelId.',id',
        ];
    }
}
