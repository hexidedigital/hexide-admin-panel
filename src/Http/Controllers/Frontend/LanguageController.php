<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Frontend;

use HexideDigital\HexideAdmin\Http\Controllers\BaseController;
use HexideDigital\HexideAdmin\Http\Middleware\LanguageMiddleware;

class LanguageController extends BaseController
{
    public function __invoke($locale = null)
    {
        $referer = redirect()->back()->getTargetUrl();
        $parse_url = parse_url($referer, PHP_URL_PATH);
        $segments = explode('/', $parse_url);

        if (isset($segments[1]) && in_array($segments[1], config('app.locales'))) {
            unset($segments[1]);
        }

        if (LanguageMiddleware::getMainLocale() !== $locale) {
            array_splice($segments, 1, 0, $locale);
        }

        $url = str_replace('/public', '', \Illuminate\Support\Facades\Request::root())
            . implode("/", $segments);
        if (parse_url($referer, PHP_URL_QUERY)) {
            $url = $url . '?' . parse_url($referer, PHP_URL_QUERY);
        }

        return redirect($url);
    }
}
