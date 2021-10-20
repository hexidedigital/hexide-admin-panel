<?php

return [


    'locale' => 'uk',
    'lang_cookie' => 'admin_locale',

    'locales' => [
        'uk',
        'ru',
        'en',
    ],

    'locales_map' => [
        'ua' => 'uk',
    ],


    'module_files' => [
        'migration', 'model', 'model_translation',
        'service', 'request', 'controller',
        'translations', 'views',
    ],

    'namespaces' => [
        'models' => 'App\\Models',
        'services' => 'App\\Services\\Backend',
        'controllers' => 'App\\Http\\Controllers\\Backend',
        'requests' => 'App\\Http\\Requests\\Backend',
        //        'repositories' => 'App\\Repositories',
    ],

    'module_paths' => [
        'admin_routes' => 'routes/admin.php',
        'adminlte_menu_translations' => 'resources/lang/vendor/XX/menu.php',
        'lang' => 'resources/lang/XX/',
        'view' => 'resources/views/admin/view/',
    ],

    // in one file | separately for each module
//    'lang_generation' => 'one_file', //'separately'

];
