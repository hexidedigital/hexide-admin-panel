<?php

use HexideDigital\HexideAdmin\Classes\Configurations\Configuration;

return [

    'configurations' => [
        'secret_key' => env('ADMIN_SECRET', null),
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
        // set size for image, if value `false` - do not set size property
        'height' => 90,
        'width' => 90,
        // see more style on Animate.css site - https://animate.style/
        'animate_name' => 'animate__bounceOut animate__infinite',
    ],

    'cards' => [
        'default-theme' => 'navy',
    ],

    'toggle' => [
        'init_class' => 'toggle_attributes',
        'styles' => [
            'status' => [
                'class' => 'toggle_attributes',
                'data-on' => '<i class="fas fa-eye"></i>',
                'data-off' => '<i class="fas fa-eye-slash"></i>',
                'data-onstyle' => 'success',
                'data-offstyle' => 'secondary',
                'data-width' => '75',
                'data-size' => 'small',
            ],
            'state_read' => [
                'class' => 'toggle_attributes',
                'data-on' => '<i class="far fa-envelope-open"></i>',
                'data-off' => '<i class="far fa-envelope"></i>',
                'data-onstyle' => 'default',
                'data-offstyle' => 'primary',
            ],
        ],
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
