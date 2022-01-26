<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use Carbon\Carbon;

class LanguageController extends BackendController
{
    public function __invoke($locale)
    {
        $path = redirect()->back()->getTargetUrl();

        $message = __('hexide-admin::messages.language.changed', [], $locale);

        if (!in_array($locale, config('hexide-admin.locales'))) {
            $locale = config('hexide-admin.locale', 'en');
            $message = __('hexide-admin::messages.language.default', [], $locale);
        }

        $this->notify(null, $message, 'info', __("hexide-admin::messages.info.title", [], $locale));

        $cookie = cookie(config('hexide-admin.lang_cookie'), $locale, Carbon::now()->diffInMinutes(Carbon::now()->addYear()));

        return redirect($path)->withCookie($cookie);
    }
}
