<?php

namespace HexideDigital\HexideAdmin\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class TranslationUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return lang_rules([
            'page' => 'nullable|string',

            '{{*}}' => 'nullable'
        ]);
    }
}
