<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use HexideDigital\ModelPermissions\Models\Permission;

class PermissionRequest extends FormRequest
{
    use AdminFormRequest;

    protected string $modelClass = Permission::class;
    protected string $routeKeyName = 'permission';

    public function rules(): array
    {
        return [
            'title' => 'required|min:3|max:100|unique:permissions,title,'.$this->modelId().',id',
        ];
    }
}
