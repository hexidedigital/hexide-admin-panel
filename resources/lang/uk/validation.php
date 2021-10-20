<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ' :attribute має бути принятий.',
    'accepted_if' => ':attribute must be accepted when :other is :value.',
    'active_url' => ' :attribute не є активним URL.',
    'after' => ' :attribute повинно бути датою після :date.',
    'after_or_equal' => ' :attribute повинно бути датою після або рівною :date.',
    'alpha' => ' :attribute може містити лише букви.',
    'alpha_dash' => ' :attribute може містити тільки букви, цифри, дефіси та символи підкреслення.',
    'alpha_num' => ' :attribute може містити тільки букви та цифри.',
    'array' => ' :attribute має бути масивом.',
    'before' => ' :attribute має бути датою до :date.',
    'before_or_equal' => ' :attribute має бути датою до або рівною :date.',
    'between' => [
        'numeric' => ' :attribute має бути між :min та :max.',
        'file' => ' :attribute має бути між :min та :max кілобайты.',
        'string' => ' :attribute має бути між :min та :max символи.',
        'array' => ' :attribute має бути між :min та :max элементами',
    ],
    'boolean' => ' :attribute поле має бути істинним або хибним.',
    'confirmed' => ' :attribute підтвердження не співпадає.',
    'current_password' => 'Пароль невірний.',
    'date' => ' :attribute не дійсна дата.',
    'date_equals' => ' :attribute має бути дата, рівна :date.',
    'date_format' => ' :attribute не відповідає формату :format.',
    'different' => ' :attribute та :other мають бути різними.',
    'digits' => ' :attribute мають бути :digits цифрами.',
    'digits_between' => ' :attribute мають бути між :min та :max цифрами.',
    'dimensions' => ' :attribute містить неприпустимі розміри зображення.',
    'distinct' => ' :attribute поле містить повторювані значення.',
    'email' => ' :attribute адрес ел. пошти має бути дійсним.',
    'ends_with' => ' :attribute має закінчуватися одним із наступних символов: :values.',
    'exists' => 'Вибрані :attribute недійсні.',
    'file' => ' :attribute має бути файлом.',
    'filled' => ' :attribute поле має містити значення.',
    'gt' => [
        'numeric' => ' :attribute має бути більшим, ніж :value.',
        'file' => ' :attribute має бути більшим, ніж :value кілобайт.',
        'string' => ' :attribute має бути більшим, ніж :value символов.',
        'array' => ' :attribute має бути більшим, ніж :value елементів.',
    ],
    'gte' => [
        'numeric' => ' :attribute має бути більшим або рівним :value.',
        'file' => ' :attribute має бути більшим або рівним :value кілобайт.',
        'string' => ' :attribute має бути більшим або рівним :value символов.',
        'array' => ' :attribute має иметь :value елементів або більшим.',
    ],
    'image' => ' :attribute має бути зображенням.',
    'in' => 'Вибрані :attribute недійсні',
    'in_array' => ' :attribute поле не существует в :other.',
    'integer' => ' :attribute має бути целым числом.',
    'ip' => ' :attribute має бути действующий IP-адрес.',
    'ipv4' => ' :attribute має бути действительным адресом IPv4.',
    'ipv6' => ' :attribute має бути действующий адрес IPv6.',
    'json' => ' :attribute має бути допустимой строкой JSON.',
    'lt' => [
        'numeric' => ' :attribute має бути меньше чем :value.',
        'file' => ' :attribute має бути меньше чем:value кілобайт.',
        'string' => ' :attribute має бути меньше чем :value символів.',
        'array' => ' :attribute має бути меньше чем :value елементів.',
    ],
    'lte' => [
        'numeric' => ' :attribute має бути менше або рівне :value.',
        'file' => ' :attribute має бути менше або рівне :value кілобайт.',
        'string' => ' :attribute має бути менше або рівний :value символів.',
        'array' => ' :attribute не має бути більше ніж :value елементів.',
    ],
    'max' => [
        'numeric' => ' :attribute не може бути більшим, ніж :max.',
        'file' => ' :attribute не може бути більшим, ніж :max кілобайт.',
        'string' => ' :attribute не може бути більшим, ніж :max символів.',
        'array' => ' :attribute не має бути більшим, ніж :max елементів.',
    ],
    'mimes' => ' :attribute має бути файл типу: :values.',
    'mimetypes' => ' :attribute має бути файл типу: :values.',
    'min' => [
        'numeric' => ' :attribute має бути не менше :min.',
        'file' => ' :attribute має бути не менше :min кілобайт.',
        'string' => ' :attribute має бути не менше :min символів.',
        'array' => ' :attribute має мати як мінімум :min елементів.',
    ],
    'multiple_of' => ':attribute має бути кратним :value.',
    'not_in' => 'Вибраний :attribute є недійснім.',
    'not_regex' => ' :attribute формат недійсний.',
    'numeric' => ' :attribute має бути числом.',
    'password' => ' Пароль невірний',
    'present' => ' :attribute поле має бути присутнім.',
    'prohibited' => ':attribute поле заборонено.',
    'prohibited_if' => ':attribute поле заборонено, коли :other є :value.',
    'prohibited_unless' => ':attribute поле заборонено, якщо :other не є :values.',
    'prohibits' => ':attribute поле забороняє присутність :other поля.',
    'regex' => ' :attribute формат недійсний.',
    'required' => ' :attribute поле, обов\'язкове для заповнення.',
    'required_if' => ' :attribute поле обов\'язкове, коли :other присутнє :value.',
    'required_unless' => ' :attribute поле обов\'язкове, якщо тільки :other присутнє в :values.',
    'required_with' => ' :attribute поле обов\'язкове, коли :values дійсне.',
    'required_with_all' => ' :attribute поле обов\'язкове, коли :values присутнє.',
    'required_without' => ' :attribute поле обов\'язкове, коли :values не присутнє.',
    'required_without_all' => ' :attribute поле обов\'язкове, якщо ні один із :values присутній.',
    'same' => ' :attribute та :other мають співпадати.',
    'size' => [
        'numeric' => ' :attribute має бути :size.',
        'file' => ' :attribute має бути :size кілобайт.',
        'string' => ' :attribute має бути :size символів.',
        'array' => ' :attribute має містити :size елементів.',
    ],
    'starts_with' => ' :attribute має починатися з одного із наступних: :values.',
    'string' => ' :attribute має бути рядком.',
    'timezone' => ' :attribute має бути дійсна часова зона.',
    'unique' => ' :attribute уже занятий.',
    'uploaded' => ' :attribute не вдалося завантажити.',
    'url' => ' :attribute має бути дійсною URL-адресою.',
    'uuid' => ' :attribute має бути дійсним UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attr_name' => [
            'rule' => 'custom rule message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [

        "slug"                   => __('admin_labels.attributes.slug'),
        "in_group_position"      => __('admin_labels.attributes.in_group_position'),
        "position"               => __('admin_labels.attributes.position'),
        "name"                   => __('admin_labels.attributes.name'),

        "uk" => [
            "title"              => __('admin_labels.attributes.title'),
            "name"               => __('admin_labels.attributes.name'),
            "description"        => __('admin_labels.attributes.description'),
            "meta_title"         => __('admin_labels.meta_attributes.title'),
            "meta_name"          => __('admin_labels.meta_attributes.name'),
            "meta_description"   => __('admin_labels.meta_attributes.description'),
            "meta_keywords"      => __('admin_labels.meta_attributes.keywords'),
        ],
        "ru" => [
            "title"              => __('admin_labels.attributes.title'),
            "name"               => __('admin_labels.attributes.name'),
            "description"        => __('admin_labels.attributes.description'),
            "meta_title"         => __('admin_labels.meta_attributes.title'),
            "meta_name"          => __('admin_labels.meta_attributes.name'),
            "meta_description"   => __('admin_labels.meta_attributes.description'),
            "meta_keywords"      => __('admin_labels.meta_attributes.keywords'),
        ],
        "en" => [
            "title"              => __('admin_labels.attributes.title'),
            "name"               => __('admin_labels.attributes.name'),
            "description"        => __('admin_labels.attributes.description'),
            "meta_title"         => __('admin_labels.meta_attributes.title'),
            "meta_name"          => __('admin_labels.meta_attributes.name'),
            "meta_description"   => __('admin_labels.meta_attributes.description'),
            "meta_keywords"      => __('admin_labels.meta_attributes.keywords'),
        ],
    ],

];
