<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use HexideDigital\HexideAdmin\Http\ActionNames;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AjaxController extends BackendController
{
    public function __invoke(Request $request): JsonResponse
    {
        $moduleName = $request->route('module_name');

        $this->setModuleName($moduleName);

        $model = 'App\\Models\\' . \Str::studly(\Str::singular($moduleName));
        $model = (new $model)::findOrFail($request->route('id'));

        $field = $request->get('field');

        if (!$model->isFillable($field)) {
            return response()->json(['message' => trans('messages.error.action')], 422);
        }

        if ($model->update([$field => $request->get('value')])) {
            return response()->json(['message' => $this->getNotifyModelMessage('success', ActionNames::Edit)]);
        }

        return response()->json(['message' => $this->getNotifyModelMessage('error', ActionNames::Edit)], 422);
    }
}
