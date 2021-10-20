<?php

namespace HexideDigital\HexideAdmin\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->permission->id ?? '';

        return [
            'title' => 'required|min:3|max:100|unique:permissions,title,'.$id.',id',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'title' => trans('permissions.attributes.title'),
        ];
    }

}
