@php
    /**
     * @var string|null $locale
     * @var int $id
     * @var \HexideDigital\HexideAdmin\Models\AdminConfiguration $admin_configuration
     */

    $attributeStoreName = $admin_configuration->storeKey();

    if ($admin_configuration->translatable) {
        $nameDotCase = "$id.$locale.$attributeStoreName";
        $nameInputCase = "{$id}[$locale][$attributeStoreName]";

        $inputValue = old($nameDotCase, $admin_configuration->translate($locale)->{$attributeStoreName} ?? null);
    } else {
        $nameDotCase = "$id.$attributeStoreName";
        $nameInputCase = "{$id}[$attributeStoreName]";

        $inputValue = old($nameDotCase, $admin_configuration->value);
    }
@endphp

@include("hexide-admin::admin.view.admin_configurations.list.types.$admin_configuration->type")

{!! $errors->first($nameDotCase, '<p class="text-red">:message</p>') !!}
{!! $errors->first($nameDotCase.'.*', '<p class="text-red">:message</p>') !!}
