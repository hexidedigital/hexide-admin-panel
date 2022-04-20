<?php

namespace HexideDigital\HexideAdmin\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class TranslationUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // todo add check for abilities
        // return \Gate::allows('translations_' . $this->group);
        return true;
    }

    public function rules(): array
    {
        return lang_rules([
            'page' => ['nullable', 'string'],

            '{{*}}' => ['nullable'],
        ]);
    }
}
