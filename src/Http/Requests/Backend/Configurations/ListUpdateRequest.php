<?php

namespace HexideDigital\HexideAdmin\Http\Requests\Backend\Configurations;

use HexideDigital\HexideAdmin\Models\AdminConfiguration;
use Illuminate\Foundation\Http\FormRequest;

class ListUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $type = "{$this->get('type')}";
        $translatable = boolval($this->get('translatable'));

        $attribute = AdminConfiguration::getStoreKey($type, $translatable);
        $attribute = $translatable ? '{{' . $attribute . '}}' : $attribute;

        $valueRule = AdminConfiguration::getRuleForType($type, $attribute);

        if ($translatable) {
            $valueRule = lang_rules($valueRule);
        }

        $rules = [
            'id'           => 'numeric|exists:admin_configurations,id',
            'type'         => 'string|in:' . implode(',', AdminConfiguration::getTypes()),
            'translatable' => 'nullable|boolean',

            $this->get('id') => $valueRule,
        ];

        return array_dot($rules);
    }
}
