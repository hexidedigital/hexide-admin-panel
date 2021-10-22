<?php

return [

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
        'index' => 'Список літаків',
        'create' => 'Додавання літака',
        'edit' => 'Редагування літака',
        'show' => 'Перегляд літака',

        // to specify your own attributes name translations
        'attributes' => [
            // append own in this place
            'flight' => 'Рейс',
        ],
    ],

    'permissions' => [
        'name' => '{1} Правило доступу|[2,*] Правила доступу',

        'index' => 'Список правил доступу',
        'create' => 'Додавання правила доступу',
        'edit' => 'Редагування правила доступу',
        'show' => 'Перегляд правила доступу',

        'attributes' => [],
    ],

    'roles' => [
        'name' => '{1} Роль|[2,*] Ролі',

        'index' => 'Список ролей',
        'create' => 'Додавання ролі',
        'edit' => 'Редагування ролі',
        'show' => 'Перегляд ролі',

        'attributes' => [],
    ],

    'users' => [
        'name' => '{1} Користувач|[2,*] Користувачі',

        'index' => 'Список користувачів',
        'create' => 'Додавання користувача',
        'edit' => 'Редагування користувача',
        'show' => 'Перегляд користувача',

        'attributes' => [
            'profile' => 'Профіль',
            'full_name' => 'ПІБ',
            'name' => 'Ім\'я',
            'last_name' => 'Прізвище',
            'middle_name' => 'По батькові',
            'phone' => 'Телефон',
            'email' => 'E-mail',
            'password' => 'Пароль',
            'password_confirmation' => 'Підтвердження паролю',
        ],
    ],

    'admin_configurations' => [
        'name' => '{1} Змінна|[2,*] Змінні',

        'index' => 'Список змінних',
        'create' => 'Додавання змінної',
        'edit' => 'Редагування змінної',
        'show' => 'Перегляд змінної',

        'type' => [
            'title' => 'Рядок',
            'text' => 'Текст',
            'image' => 'Зображення',
        ],

        'list' => 'Список змінних',
        "be_careful_when_changing" => "будьте обережні при зміні",
        "be_careful_when_saving" => "При редагуванні будьте обережні, при збереженні, зберігається лише одна форма (змінна)",
    ],

    // ----------------------------

    'pages' => [
        'name' => '{1} Сторінка|[2,*] Сторінки',

        'index' => 'Список сторінки',
        'create' => 'Додавання сторінки',
        'edit' => 'Редагування сторінки',
        'show' => 'Перегляд сторінки',

        'attributes' => [
            'parent' => 'Основна сторінка'
        ],
    ],

    'feedbacks' => [
        'name' => '{1} Відгук|[2,*] Відгуки',

        'index' => 'Список відгуків',
        'create' => 'Додавання відгуку',
        'edit' => 'Редагування відгуку',
        'show' => 'Перегляд відгуку',

        'attributes' => [
            'state_read' => 'Стан',
            'name' => 'Ім\'я',
            'phone' => 'Телефон',
            'message' => 'Повідомлення',
        ],

        'state_read' => [
            'read' => 'Прочитано',
            'unread' => 'Не прочитано',
        ],
    ],

    /*hexide_admin_stub*/

];
