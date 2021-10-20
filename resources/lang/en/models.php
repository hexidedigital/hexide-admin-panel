<?php

return [

    'add'        => 'Add',
    'edit'       => 'Edit',
    'show'       => 'View',
    'index'      => 'Table',

    // specific attributes for each module or model
    'example_' => [
        // singular and plural forms of modules
        // to get translations use method `trans_choice`
        // such as to get a translations of `example_` module we must call with next structure
        // trans_choice('models.modules.example_', 1) - to get singular form
        // trans_choice('models.modules.example_', 2) - to get plural form
        //                                (number must be greater than or equal 2)
        'name' => '{1} Aircraft|[2,*] Aircraft',

        // names for page and card titles
        // set in indefinite form
        null    => $name = 'aircraft',
        'add'   => 'Add ' . $name,
        'edit'  => 'Edit ' . $name,
        'show'  => 'View ' . $name,

        // to specify your own attributes name translations
        'attributes' => [
            // append own in this place
            'flight'                => 'Flight',
        ],
    ],

    'permissions' => [
        'name' => '{1} Permission|[2,*] Permissions',

        null    => $name = 'permission',
        'add'   => 'Add ' . $name,
        'edit'  => 'Edit ' . $name,
        'show'  => 'View ' . $name,

        'attributes' => [],
    ],

    'roles' => [
        'name' => '{1} Role|[2,*] Roles',

        null    => $name = 'role',
        'add'   => 'Add ' . $name,
        'edit'  => 'Edit ' . $name,
        'show'  => 'View ' . $name,

        'attributes' => [],
    ],

    'users' => [
        'name' => '{1} User|[2,*] Users',

        null    => $name = 'Users',
        'add'   => 'Add ' . $name,
        'edit'  => 'Edit ' . $name,
        'show'  => 'View ' . $name,

        'attributes' => [
            'profile'                => 'Profile',
            'full_name'              => 'Full name',
            'name'                   => 'Name',
            'last_name'              => 'Second name',
            'middle_name'            => 'Surname',
            'phone'                  => 'Phone',
            'email'                  => 'E-mail',
            'password'               => 'Password',
            'password_confirmation'  => 'Password confirmation',
        ],
    ],

    'variables' => [
        'name' => '{1} Variable|[2,*] Variables',

        null    => $name = 'variable',
        'add'   => 'Add ' . $name,
        'edit'  => 'Edit ' . $name,

        'type' => [
            'title'         => 'Line',
            'text'          => 'Text',
            'image'         => 'Image',
        ],

        'list'         => 'List of variables',
        "be_careful_when_changing" => "be careful when changing",
        "be_careful_when_saving" => "Be careful when editing, only one form (variable) is saved when saving",
    ],

    // ----------------------------

    'categories' => [
        'name' => '{1} Category|[2,*] Categories',

        null    => $name = 'category',
        'add'   => 'Add ' . $name,
        'edit'  => 'Edit ' . $name,
        'show'  => 'View ' . $name,

        'attributes' => [
            'parent'                    => 'Parent category',
        ],
    ],

    'menus' => [
        'name' => '{1} Menu|[2,*] Menus',

        null    => $name = 'menu',
        'add'   => 'Add ' . $name,
        'edit'  => 'Edit ' . $name,
        'show'  => 'View ' . $name,

        'attributes' => [],
    ],

    'pages' => [
        'name' => '{1} Page|[2,*] Pages',

        null    => $name = 'page',
        'add'   => 'Add ' . $name,
        'edit'  => 'Edit ' . $name,
        'show'  => 'View ' . $name,

        'attributes' => [
            'parent' => 'Parent page'
        ],
    ],

    'feedbacks' => [
        'name' => '{1} Feedback|[2,*] Feedbacks',

        null => $name = 'feedback',
        'add' => 'Add ' . $name,
        'edit' => 'Edit ' . $name,
        'show' => 'View ' . $name,

        'attributes' => [
            'state_read'    => 'State',
            'name'          => 'Name',
            'phone'         => 'Phone',
            'message'       => 'Message',
        ],

        'state_read' => [
            'read'          => 'Read',
            'unread'        => 'Not read',
        ],
    ],
];
