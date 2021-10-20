<?php

return [

    // on default create, but can be changed
    'with_table_prefix' => true,
    //
    'divider'           => '_',

    // specify model/table names which need to create
    // on first migration
    'startup_permissions' => [
        'roles', 'permissions', 'users',
        // ...
    ],

    // keys
    'roles' => [
        'admin', 'user',
    ],

    'roles_to_assign' => [
        'admin',
    ],

    'roles_admin_access' => [
        'admin',
    ],


    // on default create
    'all'  => [
        'access',
        'view',
        'create',
        'edit',
        'delete',
        'read',
    ],

    'custom' => [
        'ajax'
        // 'can_export', 'other'
    ]
];
