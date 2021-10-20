<?php

return [

    'add'        => 'Добавление',
    'edit'       => 'Редактирование',
    'show'       => 'Просмотр',
    'index'      => 'Таблица',

    // specific attributes for each module or model
    'example_' => [
        // singular and plural forms of modules
        // to get translations use method `trans_choice`
        // such as to get a translations of `example_` module we must call with next structure
        // trans_choice('models.modules.example_', 1) - to get singular form
        // trans_choice('models.modules.example_', 2) - to get plural form
        //                                (number must be greater than or equal 2)
        'name' => '{1} Самолет|[2,*] Самолеты',

        // names for page and card titles
        // set in indefinite form
        null    => $name = 'самолета',
        'add'   => 'Добавление ' . $name,
        'edit'  => 'Редактирование ' . $name,
        'show'  => 'Просмотр ' . $name,

        // to specify your own attributes name translations
        'attributes' => [
            // append own in this place
            'flight'                => 'Рейс',
        ],
    ],

    'permissions' => [
        'name' => '{1} Правило доступа|[2,*] Правила доступа',

        null    => $name = 'правила доступа',
        'add'   => 'Добавление ' . $name,
        'edit'  => 'Редактирование ' . $name,
        'show'  => 'Просмотр ' . $name,

        'attributes' => [],
    ],

    'roles' => [
        'name' => '{1} Роль|[2,*] Роли',

        null    => $name = 'роли',
        'add'   => 'Добавление ' . $name,
        'edit'  => 'Редактирование ' . $name,
        'show'  => 'Просмотр ' . $name,

        'attributes' => [],
    ],

    'users' => [
        'name' => '{1} Пользователь|[2,*] Пользователи',

        null    => $name = 'пользователя',
        'add'   => 'Добавление ' . $name,
        'edit'  => 'Редактирование ' . $name,
        'show'  => 'Просмотр ' . $name,

        'attributes' => [
            'profile'                => 'Профиль',
            'full_name'              => 'ФИО',
            'name'                   => 'Имя',
            'last_name'              => 'Фамилия',
            'middle_name'            => 'Отчество',
            'phone'                  => 'Телефон',
            'email'                  => 'E-mail',
            'password'               => 'Пароль',
            'password_confirmation'  => 'Подтверджение пароля',
        ],
    ],

    'admin_configurations' => [
        'name' => '{1} Переменная|[2,*] Переменные',

        null    => $name = 'переменной',
        'add'   => 'Добавление ' . $name,
        'edit'  => 'Редактирование ' . $name,

        'type' => [
            'title'         => 'Строка',
            'text'          => 'Текст',
            'image'         => 'Изображение',
        ],

        'list'         => 'Список переменных',
        "be_careful_when_changing" => "будьте осторожны при смене",
        "be_careful_when_saving" => "При редактировании будьте осторожны, при сохранении сохраняется только одна форма (сменная)",
    ],

    // ----------------------------

    'categories' => [
        'name' => '{1} Категория|[2,*] Категории',

        null    => $name = 'категории',
        'add'   => 'Добавление ' . $name,
        'edit'  => 'Редактирование ' . $name,
        'show'  => 'Просмотр ' . $name,

        'attributes' => [
            'parent'                    => 'Родительская категория',
        ],
    ],

    'menus' => [
        'name' => '{1} Меню|[2,*] Меню',

        null    => $name = 'меню',
        'add'   => 'Добавление ' . $name,
        'edit'  => 'Редактирование ' . $name,
        'show'  => 'Просмотр ' . $name,

        'attributes' => [],
    ],

    'pages' => [
        'name' => '{1} Страница|[2,*] Страницы',

        null    => $name = 'страницы',
        'add'   => 'Добавление ' . $name,
        'edit'  => 'Редактирование ' . $name,
        'show'  => 'Просмотр ' . $name,

        'attributes' => [
            'parent' => 'Основная страница'
        ],
    ],

    'feedbacks' => [
        'name' => '{1} Отзыв|[2,*] Отзывы',

        null    => $name = 'отзыва',
        'add'   => 'Добавление ' . $name,
        'edit'  => 'Редактирование ' . $name,
        'show'  => 'Просмотр ' . $name,

        'attributes' => [
            'state_read'    => 'Состояние',
            'name'          => 'Имя',
            'phone'         => 'Телефон',
            'message'       => 'Сообщение',
        ],

        'state_read' => [
            'read'          => 'Прочитано',
            'unread'        => 'Не прочитано',
        ],
    ],

    'template' => [
        'name' => '{1} temp|[2,*] temp',

        null    => $name = 'temp',
        'add'   => 'Добавление ' . $name,
        'edit'  => 'Редактирование ' . $name,
        'show'  => 'Просмотр ' . $name,

        'attributes' => [],
    ],

];
