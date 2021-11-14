<?php

namespace HexideDigital\HexideAdmin\Http\Traits;

trait ModuleBreadcrumbs
{
    protected function addToBreadcrumbs($method)
    {
        if (isset($method) && $this->with_breadcrumbs) {
            $module = $this->getModuleName();

            if ($method == $module) {
                $this->breadcrumbs->push(
                    trans_choice("models.$module.name", 2),
                    route("admin.$module.index")
                );
            } else if (!empty($method)) {
                $this->breadcrumbs->push(
                    __("models.$method"),
                    route("admin.$module.$method", $this->model ?? '')
                );
            }
        }
    }
}
