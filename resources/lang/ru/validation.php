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

    'accepted' => ' :attribute должен быть принят.',
    'accepted_if' => ':attribute должен быть принят, когда :other равно :value.',
    'active_url' => ' :attribute не является допустимым URL.',
    'after' => ' :attribute должен быть датой после :date.',
    'after_or_equal' => ' :attribute должен быть датой после или равной :date.',
    'alpha' => ' :attribute может содержать только буквы.',
    'alpha_dash' => ' :attribute может содержать только буквы, цифры, дефисы и символы подчеркивания.',
    'alpha_num' => ' :attribute может содержать только буквы и цифры.',
    'array' => ' :attribute должен быть массивом.',
    'before' => ' :attribute должно быть датой до :date.',
    'before_or_equal' => ' :attribute должен быть датой до или равной :date.',
    'between' => [
        'numeric' => ' :attribute должно быть между :min и :max.',
        'file' => ' :attribute должно быть между :min и :max килобайты.',
        'string' => ' :attribute должно быть между :min и :max символы.',
        'array' => ' :attribute должно быть между :min и :max элементами',
    ],
    'boolean' => ' :attribute поле должно быть истинным или ложным.',
    'confirmed' => ' :attribute подтверждение не совпадает.',
    'current_password' => 'Пароль неверен.',
    'date' => ' :attribute не действительная дата.',
    'date_equals' => ' :attribute должна быть дата, равная :date.',
    'date_format' => ' :attribute не соответствует формату :format.',
    'different' => ' :attribute и :other должны быть разными.',
    'digits' => ' :attribute должны быть :digits цифрами.',
    'digits_between' => ' :attribute должны быть между :min и :max цифрами.',
    'dimensions' => ' :attribute имеет недопустимые размеры изображения.',
    'distinct' => ' :attribute поле имеет повторяющееся значение.',
    'email' => ' :attribute адрес эл. почты должен быть действительным.',
    'ends_with' => ' :attribute должен заканчиваться одним из следующих символов: :values.',
    'exists' => 'Избранные :attribute недействительные.',
    'file' => ' :attribute должен быть файлом.',
    'filled' => ' :attribute поле должно иметь значение.',
    'gt' => [
        'numeric' => ' :attribute должно быть больше чем :value.',
        'file' => ' :attribute должно быть больше чем :value килобайт.',
        'string' => ' :attribute должно быть больше чем :value символов.',
        'array' => ' :attribute должно быть больше чем :value елементов.',
    ],
    'gte' => [
        'numeric' => ' :attribute должно быть больше или равно :value.',
        'file' => ' :attribute должно быть больше или равно :value килобайт.',
        'string' => ' :attribute должно быть больше или равно :value символов.',
        'array' => ' :attribute должен иметь :value елементов или больше.',
    ],
    'image' => ' :attribute должно быть изображение.',
    'in' => 'Избранные :attribute недействительны',
    'in_array' => ' :attribute поле не существует в :other.',
    'integer' => ' :attribute должно быть целым числом.',
    'ip' => ' :attribute должен быть действующий IP-адрес.',
    'ipv4' => ' :attribute должен быть действительным адресом IPv4.',
    'ipv6' => ' :attribute должен быть действующий адрес IPv6.',
    'json' => ' :attribute должна быть допустимой строкой JSON.',
    'lt' => [
        'numeric' => ' :attribute должно быть меньше чем :value.',
        'file' => ' :attribute должно быть меньше чем:value килобайт.',
        'string' => ' :attribute должно быть меньше чем :value символов.',
        'array' => ' :attribute должно быть меньше чем :value елементов.',
    ],
    'lte' => [
        'numeric' => ' :attribute должно быть меньше или равно :value.',
        'file' => ' :attribute должно быть меньше или равно :value килобайт.',
        'string' => ' :attribute должно быть меньше или равно :value символов.',
        'array' => ' :attribute не должно быть больше, чем :value елементов.',
    ],
    'max' => [
        'numeric' => ' :attribute не может быть больше чем :max.',
        'file' => ' :attribute не может быть больше чем :max килобайт.',
        'string' => ' :attribute не может быть больше чем :max символов.',
        'array' => ' :attribute не должно быть больше, чем :max елементов.',
    ],
    'mimes' => ' :attribute должен быть файл типа: :values.',
    'mimetypes' => ' :attribute должен быть файл типа: :values.',
    'min' => [
        'numeric' => ' :attribute должен быть не менее :min.',
        'file' => ' :attribute должен быть не менее :min килобайт.',
        'string' => ' :attribute должен быть не менее :min символов.',
        'array' => ' :attribute должен иметь как минимум :min елементов.',
    ],
    'multiple_of' => ':attribute должно быть кратно :value.',
    'not_in' => 'Выбранный :attribute является недействительным.',
    'not_regex' => ' :attribute формат недействителен.',
    'numeric' => ' :attribute должен быть числом.',
    'password' => ' Пароль неправильный',
    'present' => ' :attribute поле должно присутствовать.',
    'prohibited' => ':attribute поле запрещено.',
    'prohibited_if' => ':attribute поле запрещено, когда :other равно :value.',
    'prohibited_unless' => ':attribute поле запрещено, если :other не равно :values.',
    'prohibits' => ':attribute поле запрещает присутствие :other поля.',
    'regex' => ' :attribute формат недействителен.',
    'required' => ' :attribute поле, обязательное для заполнения.',
    'required_if' => ' :attribute поле обязательно, когда :other есть :value.',
    'required_unless' => ' :attribute поле является обязательным, если только :other есть в :values.',
    'required_with' => ' :attribute поле обязательно, когда :values настоящее.',
    'required_with_all' => ' :attribute поле обязательно, когда :values присутствуют.',
    'required_without' => ' :attribute поле обязательно, когда :values не присутствуют.',
    'required_without_all' => ' :attribute поле является обязательным, если ни один из :values присутствует.',
    'same' => ' :attribute и :other должны совпадать.',
    'size' => [
        'numeric' => ' :attribute должно быть :size.',
        'file' => ' :attribute должно быть :size килобайт.',
        'string' => ' :attribute должно быть :size символов.',
        'array' => ' :attribute должен содержать :size елементов.',
    ],
    'starts_with' => ' :attribute должен начинаться с одного из следующих: :values.',
    'string' => ' :attribute должен быть строкой.',
    'timezone' => ' :attribute должна быть действующая временная зона.',
    'unique' => ' :attribute уже занят.',
    'uploaded' => ' :attribute не удалось загрузить.',
    'url' => ' :attribute должен быть действующий URL.',
    'uuid' => ' :attribute должен быть действующий UUID.',

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
