<?php

return [

    'index' => 'List',
    'create' => 'Creating',
    'edit' => 'Editing',
    'show' => 'View',

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
        'index' => 'List of aircraft',
        'create' => 'Creating a aircraft',
        'edit' => 'Aircraft editing',
        'show' => 'View aircraft',

        // to specify your own attributes name translations
        'attributes' => [
            // append own in this place
            'flight' => 'Flight',
        ],
    ],

    'permissions' => [
        'name' => '{1} Permission|[2,*] Permissions',

        'index' => 'List of permissions',
        'create' => 'Creating a permission',
        'edit' => 'Permission editing',
        'show' => 'View permission',

        'attributes' => [],
    ],

    'roles' => [
        'name' => '{1} Role|[2,*] Roles',

        'index' => 'List of roles',
        'create' => 'Creating a role',
        'edit' => 'Role editing',
        'show' => 'View role',

        'attributes' => [],
    ],

    'users' => [
        'name' => '{1} User|[2,*] Users',

        'index' => 'List of users',
        'create' => 'Creating a user',
        'edit' => 'User editing',
        'show' => 'View user',

        'attributes' => [
            'profile' => 'Profile',
            'full_name' => 'Full name',
            'name' => 'Name',
            'last_name' => 'Second name',
            'middle_name' => 'Surname',
            'phone' => 'Phone',
            'email' => 'E-mail',
            'password' => 'Password',
            'password_confirmation' => 'Password confirmation',
        ],
    ],

    'admin_configurations' => [
        'name' => '{1} Variable|[2,*] Variables',

        'index' => 'List of variables',
        'create' => 'Creating a variable',
        'edit' => 'Page variable',
        'show' => 'Variable page',
        'type' => [
            'title' => 'Line',
            'text' => 'Text',
            'image' => 'Image',
        ],

        'list' => 'List of variables',
        "be_careful_when_changing" => "be careful when changing",
        "be_careful_when_saving" => "Be careful when editing, only one form (variable) is saved when saving",
    ],

    // ----------------------------

    'pages' => [
        'name' => '{1} Page|[2,*] Pages',

        'index' => 'List of pages',
        'create' => 'Creating a page',
        'edit' => 'Page editing',
        'show' => 'View page',

        'attributes' => [
            'parent' => 'Parent page'
        ],
    ],

    'feedbacks' => [
        'name' => '{1} Feedback|[2,*] Feedbacks',

        'index' => 'List of feedbacks',
        'create' => 'Creating feedback',
        'edit' => 'Feedback editing',
        'show' => 'View feedback',

        'attributes' => [
            'state_read' => 'State',
            'name' => 'Name',
            'phone' => 'Phone',
            'message' => 'Message',
        ],

        'state_read' => [
            'read' => 'Read',
            'unread' => 'Not read',
        ],
    ],

    /*hexide_admin_stub*/

];
