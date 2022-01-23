const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

if (mix.inProduction()) {
    mix.version();
}

mix.disableSuccessNotifications();

const path = 'build/';

mix.setPublicPath(path)
    .js('resources/js/admin/app.js', 'js/admin/app.min.js')
    .js('resources/js/admin/alpine.js', '/js/admin/alpine.min.js')
    .sass('resources/sass/admin/app.scss', 'css/admin/app.min.css')
    .sourceMaps()
