<?php

namespace HexideDigital\HexideAdmin\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class AjaxFieldRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'numeric'],
            'field' => ['required', 'string'],
            'value' => ['nullable'],
        ];
    }
}
