<?php

namespace HexideDigital\HexideAdmin\Http\Requests\Backend;

use App\Models\User;
use HexideDigital\ModelPermissions\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (isset($this->user)) {
            return Gate::allows(Permission::Update, User::find($this->user));
        } else {
            return Gate::allows(Permission::Create, User::class);
        }
    }

    public function rules(): array
    {
        $model = $this->user ?? '';

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $model . ',id',
            'password' => (!empty($model) ? 'nullable' : 'required') . '|confirmed|min:' . User::password_min_length,
            'roles' => 'nullable|array',
            'roles.*' => 'integer',
        ];
    }
}
