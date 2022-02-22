<?php

namespace HexideDigital\HexideAdmin\Http\Requests\Backend\Configurations;

use HexideDigital\HexideAdmin\Classes\Configurations\Configuration;
use HexideDigital\HexideAdmin\Models\AdminConfiguration;
use HexideDigital\ModelPermissions\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ListUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::any([
           Permission::Update,
           'list'
        ], AdminConfiguration::find($this->get('id')));
    }

    public function rules(): array
    {
        $type = "{$this->get('type')}";
        $translatable = boolval($this->get('translatable'));

        $configuration = \App::make(Configuration::class);

        $attribute = $configuration->getStoreKey($type, $translatable);
        $attribute = $translatable ? '{{' . $attribute . '}}' : $attribute;

        $valueRule = $configuration->getRuleForType($type, $attribute);

        if ($translatable) {
            $valueRule = lang_rules($valueRule);
        }

        $rules = [
            'id'           => 'numeric|exists:admin_configurations,id',
            'type'         => 'string|in:' . $configuration->implodeTypes(),
            'translatable' => 'nullable|boolean',

            $this->get('id') => $valueRule,
        ];

        return array_dot($rules);
    }
}
