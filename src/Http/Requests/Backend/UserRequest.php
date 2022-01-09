<?php

namespace HexideDigital\HexideAdmin\Http\Requests\Backend;

use App\Models\User;
use HexideDigital\ModelPermissions\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return \Gate::any([
            Permission::key('users', Permission::Create),
            Permission::key('users', Permission::Edit),
        ]);
    }

    public function rules(): array
    {
        $model = $this->user ?? '';
        if(isset($model->id) && ($model instanceof User)){$model = $model->id;}

        return [
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email,'.$model.',id',
            'password'   => (!empty($model) ? 'nullable' : 'required').'|confirmed|min:'.User::password_min_length,
            'roles'      => 'nullable|array',
            'roles.*'    => 'integer',
        ];
    }
}
