<?php

namespace HexideDigital\HexideAdmin\Services\Backend;

use HexideDigital\HexideAdmin\Models\AdminConfiguration;
use HexideDigital\HexideAdmin\Services\BackendService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfigurationService extends BackendService
{
    /**
     * @param Request $request
     * @param AdminConfiguration $model
     *
     * @return void
     */
    public function deleteModel(Request $request, Model $model): void
    {
        $storage = Storage::disk('public');

        /* todo check if file type for remove files */
        if ($model->type == AdminConfiguration::IMAGE) {
            if ($model->translatable) {
                foreach ($this->locales as $locale) {
                    $storage->delete($model->translate($locale)->text);
                }
            } else {
                $storage->delete($model->value);
            }
        }

        $model->delete();
    }
}
