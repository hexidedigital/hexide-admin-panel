<?php

namespace HexideDigital\HexideAdmin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LanguageMiddleware
{
    public static function getMainLocale(): string
    {
//        config('app.locale');
        return 'uk';
    }

    public static function getLocaleFromUrl()
    {
        $languages = config('app.locales');

        $mainLanguage = self::getMainLocale();

        $uri = \Request::path();
        $segmentsURI = explode('/', $uri);

        if (\Request::acceptsJson()) {
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

    public function handle(Request $request, Closure $next, ...$args)
    {
        $locale = config('app.locale');

        $available = config('app.locales');
        $default = config('app.locale');

        if (in_array('admin', $args)) {
            $locale = \Cookie::get(config('hexide-admin.lang_cookie', 'admin_locale'));
            $available = config('hexide-admin.locales');
            $default = config('hexide-admin.locale');
        } elseif (in_array('api', $args)) {
            $locale = $request->header(config('hexide-admin.lang_header', 'X-localization'));
        } elseif (in_array('web', $args)) {
            $locale = self::getLocaleFromUrl();
        }

        if (!in_array($locale, $available)) {
            $locale = $default;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
