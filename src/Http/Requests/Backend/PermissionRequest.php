<?php

namespace HexideDigital\HexideAdmin\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->permission->id ?? '';

        return [
            'title' => 'required|min:3|max:100|unique:permissions,title,'.$id.',id',
        ];
    }
}
