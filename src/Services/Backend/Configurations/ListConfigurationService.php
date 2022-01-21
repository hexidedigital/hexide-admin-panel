<?php

namespace HexideDigital\HexideAdmin\Services\Backend\Configurations;

use HexideDigital\HexideAdmin\Classes\Configurations\Configuration;
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

        // update translated fields after processing as file, object or array type
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
        $inputs = $request->validated();
        $inputs = array_merge($inputs, $inputs[$model->id]);
        // after array merge, id-key is transformed to 0-key
        unset($inputs[0]);

        $configuration = app(Configuration::class);

        if ($configuration->canStoreFiles($model->type)) {
            $key = $model->storeKey();

            if ($model->translatable) {
                foreach ($this->locales as $locale) {
                    $storePath = $this->getStorePath($model->type, $key, $locale);

                    Arr::set($inputs, $storePath, $this->saveUploadedFile($model, $request, $key, $locale));
                }
            } else {
                $storePath = $this->getStorePath($model->type, $key);

                Arr::set($inputs, $storePath, $this->saveUploadedFile($model, $request, $key));
            }
        }

        $model->update($inputs);

        return $model;
    }

    public function saveUploadedFile(
        AdminConfiguration $model,
        Request            $request,
        string             $key,
        ?string            $locale = null
    )
    {
        $oldValue = $this->getOldValue($model, $locale);

        $fieldPath = isset($locale) ? "$locale.$key" : $key;

        $options = [
            'folder' => \Str::lower($model->type),
            'old_path' => $oldValue,
        ];

        if (false !== $path = $this->handleUploadedFile(
                $request->file($model->id . ".$fieldPath." . $this->fieldPathForGetInputValue($model->type)),
                $request->input($model->id . ".$fieldPath." . $this->getRemoveKeyForType($model->type)),
                $options)
        ) {
            return $path;
        }

        return $oldValue;
    }

    public function fieldPathForGetInputValue(string $type): string
    {
        if (in_array($type, [Configuration::IMAGE, Configuration::FILE])) {
            return $type;
        }

        if (Configuration::IMG_BUTTON === $type) {
            return 'image';
        }

        return '';
    }

    public function getStorePath(string $type, string $key, ?string $locale = null): string
    {
        $path = isset($locale) ? "$locale.$key" : $key;

        if (in_array($type, [Configuration::IMAGE, Configuration::FILE])) {
            return $path;
        }

        if (Configuration::IMG_BUTTON === $type) {
            return "$path.image";
        }

        return $path;
    }

    public function getOldValue(AdminConfiguration $model, ?string $locale = null): ?string
    {
        $key = $model->storeKey();

        $oldValue = $model->translatable
            ? $model->translate($locale)->getAttribute($key) ?? null
            : $model->{$key} ?? null;

        if ($model->isType(Configuration::IMG_BUTTON)) {
            $oldValue = Arr::get(Arr::wrap($oldValue), 'image');
        }

        if (is_array($oldValue)) {
            return null;
        }

        return $oldValue;
    }

    public function getRemoveKeyForType(string $type): ?string
    {
        if (Configuration::IMG_BUTTON === $type || Configuration::IMAGE === $type) {
            return 'isRemoveImage';
        }

        if (Configuration::FILE === $type) {
            return 'isRemoveFile';
        }

        return null;
    }
}
