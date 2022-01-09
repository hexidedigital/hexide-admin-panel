<?php

namespace HexideDigital\HexideAdmin\Services\Backend;

use HexideDigital\HexideAdmin\Http\Requests\Backend\Configurations\ListUpdateRequest;
use HexideDigital\HexideAdmin\Models\AdminConfiguration;
use HexideDigital\HexideAdmin\Services\BackendService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ListConfigurationService extends BackendService
{
    /**
     * @param ListUpdateRequest $request
     * @param AdminConfiguration $model
     *
     * @return array
     */
    public function processInputData(Request $request, Model $model): array
    {
        $inputs = $request->validated();

        $inputs = array_merge($inputs, $inputs[$model->id]);
        unset($inputs[$model->id]);

        return $inputs;
    }

    /**
     * @param ListUpdateRequest $request
     * @param AdminConfiguration $model
     *
     * @return AdminConfiguration
     */
    public function postHandle(Request $request, Model $model): Model
    {
        $inputs = $this->inputData;

        // todo store files and images
        // code bellow is deprecated
        if ($model->type === AdminConfiguration::IMAGE) {

            if ($model->translatable) {

                foreach ($this->locales as $locale) {
                    $keyPath = "$model->id.$locale.content";
                    $removeKey = "$locale.isRemoveImage";
                    $value = $model->translate($locale)->text ?? '';

                    if ($request->hasFile($keyPath) || Arr::get($inputs, $removeKey)) {
                        $path = $this->saveImage($request->file($keyPath), $model->key, $value);

                        $inputs[$locale]['content'] = $path;
                    }
                }

            } else {
                $keyPath = "$model->id.content";
                $removeKey = "isRemoveImage";
                $value = $model->value ?? '';

                if ($request->hasFile($keyPath) || Arr::get($inputs, $removeKey)) {
                    $path = $this->saveImage($request->file($keyPath), $model->key, $value);

                    $inputs['value'] = $path;
                }
            }
        }

        $model->update($inputs);

        return $model;
    }
}
