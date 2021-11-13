<?php

return [

    'secret_key' => env('ADMIN_SECRET', null),

    'locale' => 'uk',
    'lang_cookie' => 'admin_locale',

    // locales only for admin panel
    'locales' => [
        'uk',
        'ru',
        'en',
    ],

    // if you need to use another locale key for some logic
    // use this map to define it
    'locales_map' => [
//        'uk' => 'ua',
    ],

    // in this place you can edit recommended namespaces for files
    'namespaces' => [
        'model' => 'App\\Models',
        'request' => 'App\\Http\\Requests\\Backend',
        'service' => 'App\\Services\\Backend',
        'controller' => 'App\\Http\\Controllers\\Backend',
    ],

    // add this line to the file to indicate where to generate the code
    // /*hexide_admin_stub*/

    'module_paths' => [
        'adminlte_menu_translations' => ['resources/lang/vendor/adminlte/', 'menu.php'],
        'lang' => ['resources/lang/', 'models.php'],

        // it is possible to specify more such types
        // only this stub /*hexide_admin_stub*/ or combine with these two
        // /*hexide_admin_stub-resource*/ and /*hexide_admin_stub-ajax*/
        'admin_route' => 'routes/admin.php',

        // no need to set stub-key
        // will simply be placed in this place module blade views
        'views' => 'resources/views/admin/view/',
    ],

];
