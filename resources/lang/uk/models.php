<?php

return [

    'add'        => 'Додавання',
    'edit'       => 'Редагування',
    'show'       => 'Перегляд',
    'index'      => 'Таблиця',

    // specific attributes for each module or model
    'example_' => [
        // singular and plural forms of modules
        // to get translations use method `trans_choice`
        // such as to get a translations of `example_` module we must call with next structure
        // trans_choice('models.modules.example_', 1) - to get singular form
        // trans_choice('models.modules.example_', 2) - to get plural form
        //                                (number must be greater than or equal 2)
        'name' => '{1} Літак|[2,*] Літаків',

        // names for page and card titles
        // set in indefinite form
        null    => $name = 'літака',
        'add'   => 'Додавання ' . $name,
        'edit'  => 'Редагування ' . $name,
        'show'  => 'Перегляд ' . $name,

        // to specify your own attributes name translations
        'attributes' => [
            // append own in this place
            'flight'                => 'Рейс',
        ],
    ],

    'permissions' => [
        'name' => '{1} Правило доступу|[2,*] Правила доступу',

        null    => $name = 'правила доступу',
        'add'   => 'Додавання ' . $name,
        'edit'  => 'Редагування ' . $name,
        'show'  => 'Перегляд ' . $name,

        'attributes' => [],
    ],

    'roles' => [
        'name' => '{1} Роль|[2,*] Ролі',

        null    => $name = 'ролі',
        'add'   => 'Додавання ' . $name,
        'edit'  => 'Редагування ' . $name,
        'show'  => 'Перегляд ' . $name,

        'attributes' => [],
    ],

    'users' => [
        'name' => '{1} Користувач|[2,*] Користувачі',

        null    => $name = 'користувача',
        'add'   => 'Додавання ' . $name,
        'edit'  => 'Редагування ' . $name,
        'show'  => 'Перегляд ' . $name,

        'attributes' => [
            'profile'                => 'Профіль',
            'full_name'              => 'ПІБ',
            'name'                   => 'Ім\'я',
            'last_name'              => 'Прізвище',
            'middle_name'            => 'По батькові',
            'phone'                  => 'Телефон',
            'email'                  => 'E-mail',
            'password'               => 'Пароль',
            'password_confirmation'  => 'Підтвердження паролю',
        ],
    ],

    'admin_configurations' => [
        'name' => '{1} Змінна|[2,*] Змінні',

        null    => $name = 'змінної',
        'add'   => 'Додавання ' . $name,
        'edit'  => 'Редагування ' . $name,

        'type' => [
            'title'         => 'Рядок',
            'text'          => 'Текст',
            'image'         => 'Зображення',
        ],

        'list'         => 'Список змінних',
        "be_careful_when_changing" => "будьте обережні при зміні",
        "be_careful_when_saving" => "При редагуванні будьте обережні, при збереженні, зберігається лише одна форма (змінна)",
    ],

    // ----------------------------

    'categories' => [
        'name' => '{1} Категорія|[2,*] Категорії',

        null    => $name = 'категорії',
        'add'   => 'Додавання ' . $name,
        'edit'  => 'Редагування ' . $name,
        'show'  => 'Перегляд ' . $name,

        'attributes' => [
            'parent'                    => 'Батьківська категорія',
        ],
    ],

    'menus' => [
        'name' => '{1} Меню|[2,*] Меню',

        null    => $name = 'меню',
        'add'   => 'Додавання ' . $name,
        'edit'  => 'Редагування ' . $name,
        'show'  => 'Перегляд ' . $name,

        'attributes' => [],
    ],

    'pages' => [
        'name' => '{1} Сторінка|[2,*] Сторінки',

        null    => $name = 'сторінки',
        'add'   => 'Додавання ' . $name,
        'edit'  => 'Редагування ' . $name,
        'show'  => 'Перегляд ' . $name,

        'attributes' => [
            'parent' => 'Основна сторінка'
        ],
    ],

    'feedbacks' => [
        'name' => '{1} Відгук|[2,*] Відгуки',

        null    => $name = 'відгука',
        'add'   => 'Додавання ' . $name,
        'edit'  => 'Редагування ' . $name,
        'show'  => 'Перегляд ' . $name,

        'attributes' => [
            'state_read'    => 'Стан',
            'name'          => 'Ім\'я',
            'phone'         => 'Телефон',
            'message'       => 'Повідомлення',
        ],

        'state_read' => [
            'read'          => 'Прочитано',
            'unread'        => 'Не прочитано',
        ],

    ],

    'template' => [
        'name' => '{1} temp|[2,*] temp',

        null    => $name = 'temp',
        'add'   => 'Додавання ' . $name,
        'edit'  => 'Редагування ' . $name,
        'show'  => 'Перегляд ' . $name,

        'attributes' => [],
    ],

];
