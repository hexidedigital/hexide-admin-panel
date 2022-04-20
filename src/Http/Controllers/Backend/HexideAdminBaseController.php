<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use Illuminate\Support\Str;
use View;

/**
 * Only for package, in project You should use BackendController
 */
abstract class HexideAdminBaseController extends BackendController
{
    protected function guessViewName(string $view): string
    {
        $module = Str::snake($this->getModuleName());

        if (View::exists($viewPath = "hexide-admin::admin.view.$module.$view")) {
            return $viewPath;
        }

        if (View::exists($viewPath = "hexide-admin::admin.view.$view")) {
            return $viewPath;
        }

        return $view;
    }
}
