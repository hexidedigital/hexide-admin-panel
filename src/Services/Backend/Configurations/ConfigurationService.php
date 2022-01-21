<?php

namespace HexideDigital\HexideAdmin\Services\Backend\Configurations;

use HexideDigital\HexideAdmin\Models\AdminConfiguration;
use HexideDigital\HexideAdmin\Services\BackendService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use HexideDigital\HexideAdmin\Classes\Configurations\Configuration;

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
        $configuration = app(Configuration::class);

        if ($configuration->canStoreFiles($model->type)) {
            if ($model->translatable) {
                foreach ($this->locales as $locale) {
                    $field = $model->storeKey();

                    $this->storageDelete($model, $model->translate($locale)->attributes[$field] ?? null);
                }
            } else {
                $this->storageDelete($model, $model->value);
            }
        }

        $model->delete();
    }

    protected function storageDelete(AdminConfiguration $model, $value)
    {
        $storage = Storage::disk('public');

        if ($model->isType(Configuration::IMG_BUTTON)) {
            $value = array_get($value, 'image');
        }

        $storage->delete($value);
    }
}
