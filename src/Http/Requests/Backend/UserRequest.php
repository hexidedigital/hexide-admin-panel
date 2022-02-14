<?php

namespace HexideDigital\HexideAdmin\Http\Requests\Backend;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    use AdminFormRequest;

    protected string $modelClass = User::class;
    protected string $routeKeyName = 'user';

    public function rules(): array
    {
        $model = $this->modelId();

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $model . ',id',
            'password' => (!empty($model) ? 'nullable' : 'required') . '|confirmed|min:' . User::password_min_length,
            'roles' => 'nullable|array',
            'roles.*' => 'integer',
        ];
    }
}
