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
        'name' => '{1} Самолет|[2,*] Самолеты',

        // names for page and card titles
        'index' => 'Список самолетов',
        'create' => 'Добавление самолета',
        'edit' => 'Редактирование самолета',
        'show' => 'Просмотр самолета',

        // to specify your own attributes name translations
        'attributes' => [
            // append own in this place
            'flight' => 'Рейс',
        ],
    ],

    'permissions' => [
        'name' => '{1} Правило доступа|[2,*] Правила доступа',

        'index' => 'Список правил доступа',
        'create' => 'Добавление правила доступа',
        'edit' => 'Редактирование правила доступа',
        'show' => 'Просмотр правила доступа',


        'attributes' => [],
    ],

    'roles' => [
        'name' => '{1} Роль|[2,*] Роли',

        'index' => 'Список ролей',
        'create' => 'Добавление роли',
        'edit' => 'Редактирование роли',
        'show' => 'Просмотр роли',


        'attributes' => [],
    ],

    'users' => [
        'name' => '{1} Пользователь|[2,*] Пользователи',

        'index' => 'Список пользователей',
        'create' => 'Добавление пользователя',
        'edit' => 'Редактирование пользователя',
        'show' => 'Просмотр пользователя',

        'attributes' => [
            'profile' => 'Профиль',
            'full_name' => 'ФИО',
            'name' => 'Имя',
            'last_name' => 'Фамилия',
            'middle_name' => 'Отчество',
            'phone' => 'Телефон',
            'email' => 'E-mail',
            'password' => 'Пароль',
            'password_confirmation' => 'Подтверджение пароля',
        ],
    ],

    'admin_configurations' => [
        'name' => '{1} Переменная|[2,*] Переменные',

        'index' => 'Список переменных',
        'create' => 'Добавление переменной',
        'edit' => 'Редактирование переменной',
        'show' => 'Просмотр переменной',

        'type' => [
            'title' => 'Строка',
            'text' => 'Текст',
            'image' => 'Изображение',
        ],

        'list' => 'Список переменных',
        "be_careful_when_changing" => "будьте осторожны при смене",
        "be_careful_when_saving" => "При редактировании будьте осторожны, при сохранении сохраняется только одна форма (сменная)",
    ],

    // ----------------------------

    'pages' => [
        'name' => '{1} Страница|[2,*] Страницы',

        'index' => 'Список страниц',
        'create' => 'Добавление страницы',
        'edit' => 'Редактирование страницы',
        'show' => 'Просмотр страницы',

        'attributes' => [
            'parent' => 'Основная страница'
        ],
    ],

    'feedbacks' => [
        'name' => '{1} Отзыв|[2,*] Отзывы',

        'index' => 'Список отзывов',
        'create' => 'Добавление отзыва',
        'edit' => 'Редактирование отзыва',
        'show' => 'Просмотр отзыва',

        'attributes' => [
            'state_read' => 'Состояние',
            'name' => 'Имя',
            'phone' => 'Телефон',
            'message' => 'Сообщение',
        ],

        'state_read' => [
            'read' => 'Прочитано',
            'unread' => 'Не прочитано',
        ],
    ],

    /*hexide_admin_stub*/

];
