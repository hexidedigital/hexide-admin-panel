<?php

declare(strict_types=1);

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
            'list',
        ], AdminConfiguration::find($this->get('id')));
    }

    public function rules(): array
    {
        $type = (string)$this->get('type');
        $translatable = boolval($this->get('translatable'));

        $configuration = \App::make(Configuration::class);

        $attribute = $configuration->getStoreKey($type, $translatable);
        $attribute = $translatable
            ? config('translatable.rule_factory.prefix', '{{') . $attribute . config('translatable.rule_factory.suffix', '}}')
            : $attribute;

        $valueRule = $configuration->getRuleForType($type, $attribute);

        if ($translatable) {
            $valueRule = lang_rules($valueRule);
        }

        $rules = [
            'id'           => ['numeric', 'exists:admin_configurations,id'],
            'type'         => ['string', 'in:' . $configuration->implodeTypes()],
            'translatable' => ['nullable', 'boolean'],
        ];

        $id = $this->get('id');
        foreach ($valueRule as $key => $value) {
            $rules[$id . '.' . $key] = $value;
        }

        return $rules;
    }
}
