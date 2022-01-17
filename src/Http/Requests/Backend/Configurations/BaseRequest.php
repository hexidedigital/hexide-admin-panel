<?php

namespace HexideDigital\HexideAdmin\Http\Requests\Backend\Configurations;

use HexideDigital\HexideAdmin\Classes\Configurations\Configuration;
use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'              => 'required|string|in:' . app(Configuration::class)->implodeTypes(),
            'key'               => 'required|string|max:10000',
            'name'              => 'required|string|max:10000',
            'translatable'      => 'required|boolean',
            'description'       => 'nullable|string|max:10000',
            'group'             => 'nullable|string|max:10000',
            'in_group_position' => 'numeric|max:10000|min:1',
        ];
    }
}
