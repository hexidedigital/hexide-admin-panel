<?php

namespace HexideDigital\HexideAdmin\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|min:3|max:100',
            'admin_access' => 'required|bool',
            'permissions' => 'nullable|array',
        ];
    }
}
