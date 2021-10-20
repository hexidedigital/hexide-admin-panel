<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;


use Carbon\Carbon;
use HexideDigital\HexideAdmin\Http\Controllers\BaseController;

class LanguageController extends BaseController
{
    public function __invoke($locale)
    {
        $path = redirect()->back()->getTargetUrl();

        $message = __('messages.language.changed', [], $locale);

        if(!in_array($locale, config('hexide_admin.locales'))){
            $locale = config('hexide_admin.locale');
            $message = __('messages.language.default', [], $locale);
        }

        toastr($message, 'info');

        $cookie = cookie(config('hexide_admin.lang_cookie'), $locale, Carbon::now()->diffInMinutes(Carbon::now()->addYear()));

        return redirect($path)->withCookie($cookie);
    }
}