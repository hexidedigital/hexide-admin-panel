<?php

namespace HexideDigital\HexideAdmin\Http\Middleware;

use Closure;
use Request;

class LanguageMiddleware
{
    public static function getMainLocale()
    {
//        config('app.locale');
        return 'uk';
    }

    public static function getLocaleFromUrl($guard)
    {
        $languages = config('app.locales');

        $mainLanguage = self::getMainLocale();

        $uri = Request::path();
        $segmentsURI = explode('/', $uri);

        if (Request::acceptsJson()) {

            if (!empty($segmentsURI[1]) && in_array($segmentsURI[1], $languages)) {

                if ($segmentsURI[1] != $mainLanguage) {
                    app()->setLocale($segmentsURI[1]);

                    return $segmentsURI[1];
                }
            }
        }

        if (!empty($segmentsURI[0]) && in_array($segmentsURI[0], $languages)) {

            if ($segmentsURI[0] != $mainLanguage) {
                app()->setLocale($segmentsURI[0]);

                return $segmentsURI[0];
            }
        }

        return null;
    }

    public function handle(\Illuminate\Http\Request $request, Closure $next, ...$args)
    {
        if (in_array('admin', $args)) {

            $locale = \Cookie::get(config('hexide_admin.lang_cookie'), config('hexide_admin.locale'));

            if (!in_array($locale, config('hexide_admin.locales'))) {
                $locale = config('hexide_admin.locale');
            }

            app()->setLocale($locale);

        } elseif (in_array('api', $args)) {

            $locale = $request->header('X-localization', config('app.locale'));

            app()->setLocale($locale);

        } elseif (in_array('web', $args)) {

            $locale = self::getLocaleFromUrl('web');

            if ($locale) app()->setLocale($locale);
            else app()->setLocale(config('app.locale'));

        }

        return $next($request);
    }

}
