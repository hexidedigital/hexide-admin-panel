<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use Carbon\Carbon;
use HexideDigital\HexideAdmin\Http\Controllers\BaseController;

class LanguageController extends BaseController
{
    public function __invoke($locale)
    {
        $path = redirect()->back()->getTargetUrl();

        $message = __('hexide-admin::messages.language.changed', [], $locale);

        if (!in_array($locale, config('hexide-admin.locales'))) {
            $locale = config('hexide-admin.locale');
            $message = __('hexide-admin::messages.language.default', [], $locale);
        }

        toastr($message, 'info');

        $cookie = cookie(config('hexide-admin.lang_cookie'), $locale, Carbon::now()->diffInMinutes(Carbon::now()->addYear()));

        return redirect($path)->withCookie($cookie);
    }
}
