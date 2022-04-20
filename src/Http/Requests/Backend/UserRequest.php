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
        $modelId = $this->modelId();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$modelId.',id'],
            /* todo refactor and research to check request method, but not empty */
            'password' => [(!empty($modelId) ? 'nullable' : 'required'), 'confirmed', 'min:' . User::password_min_length],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer'],
        ];
    }
}
