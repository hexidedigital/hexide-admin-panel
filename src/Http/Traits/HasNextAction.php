<?php

namespace HexideDigital\HexideAdmin\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Str;

trait HasNextAction
{
    protected function getActionsForView(): array
    {
        return [
            'default' => [
                'index' => __('next_action.index'),
            ],
            'menu' => [
                'edit' => __('next_action.edit'),
                'create' => __('next_action.create'),
//                'show' => __('Save and view'),
            ],
        ];
    }

    protected function next_action(string $module, Model $model = null, array $params = []): RedirectResponse
    {
        $next_action = request('next_action', 'index');

        if(in_array($next_action, ['edit', 'show'])){
            $params = array_merge([ Str::singular($module) => $model], $params);
        }

        return redirect()->route("admin.$module.$next_action", $params);
    }
}
