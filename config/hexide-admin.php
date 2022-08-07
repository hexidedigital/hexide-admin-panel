<?php

use HexideDigital\HexideAdmin\Classes\Configurations\Configuration;

return [

    'configurations' => [
        'secret_key' => null, //env('ADMIN_SECRET', null),
        'show_admin_header' => true,
        'show_debug_footer_site' => false,
        'show_debug_footer_admin' => true,
    ],

    'routes' => [
        'admin' => [
            'dashboard' => 'home',
            'prefix' => 'admin',
            'middleware' => ['web', 'auth:admin', 'language:admin'],
        ],
        'ajax' => [
            'middleware' => ['ajax'],
            'prefix' => 'ajax_field',
        ],
    ],

    // -------------------------------
    // Localisation

    'locale' => 'uk',
    'lang_cookie' => 'admin_locale',
    'lang_header' => 'X-localization',

    // locales only for admin panel
    'locales' => [
        'uk',
        // 'ru',
        'en',
    ],

    // -------------------------------
    // Generate command

    // in this place you can edit recommended namespaces for files
    'namespaces' => [
        'model' => 'Models',
        'policy' => 'Policies',
        'request' => 'Http\\Requests\\Backend',
        'service' => 'Services\\Backend',
        'controller' => 'Http\\Controllers\\Backend',
        'livewire-table' => 'Http\\Livewire\\Admin\\Tables',
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

    // -------------------------------
    // Some design properties

    'with_preloader' => true,
    'preloader' => [
        'image' => 'img/HexideDigitalLogo.png',
        'height' => 90,
        'width' => 90,
        'animate_name' => 'bounceOut animate__infinite',
    ],

    // -------------------------------
    // Variables, admin configurations

    'variables' => [
        'key_1' => [
            // 'key' => 'key_1',
            'name' => 'Name',
            'type' => Configuration::TEXT,
            // 'translatable' => true
            'localization' => [
                'uk' => 'Some value or json string',
            ],
            // 'plain_value' => 'Plain text or html',
        ],
    ],

];
