<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Services;

use FileUploader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class BackendService implements ServiceInterface
{
    protected bool $withImage = false;
    protected array $locales;
    protected ?Model $model;
    protected Request $request;
    protected array $inputData;

    public function __construct()
    {
        $this->initService();
    }

    public function initService()
    {
        $this->locales = config('translatable.locales');
    }

    public function handleWithImage(bool $flag = true)
    {
        $this->withImage = $flag;
    }

    public function handleRequest(Request $request, Model $model): Model
    {
        $this->request = $request;

        $this->inputData = $this->processInputData($this->request, $model);

        $this->model = $this->saveModel($this->inputData, $model);

        $this->model = $this->postHandle($this->request, $model);

        return $this->model;
    }

    /**
     * @param FormRequest|Request $request
     * @param Model $model
     *
     * @return array
     */
    public function processInputData(Request $request, Model $model): array
    {
        $inputs = $request instanceof FormRequest
            ? $request->validated()
            : $request->all();

        $inputs['status'] = $inputs['status'] ?? false;

        return $inputs;
    }

    public function saveModel(array $attributes, Model $model): Model
    {
        $model->fill($attributes);

        $model->save();

        return $model->refresh();
    }

    public function postHandle(Request $request, Model $model): Model
    {
        if ($this->withImage) {
            if (false !== $path = $this->handleOneImage($request->all())) {
                $model->update(['image' => $path]);
            }
        }

        return $model;
    }

    public function deleteModel(Request $request, Model $model): void
    {
        if (!$model->delete()) {
            throw new \Exception('Model not deleted');
        }

        if ($this->withImage && !$this->modelUsesSoftDeletesTrait($model)) {
            $this->deleteFile($model->image ?? null);
        }
    }

    /**
     * @param Request $request
     * @param Model|SoftDeletes $model
     *
     * @return void
     * @throws \Exception
     */
    public function restoreModel(Request $request, Model $model): void
    {
        if (!$this->modelUsesSoftDeletesTrait($model)) {
            throw new \Exception('Model class not uses SoftDeletes trait');
        }

        if (!$model->restore()) {
            throw new \Exception('Model not restored');
        }
    }

    /**
     * @param Request $request
     * @param Model|SoftDeletes $model
     *
     * @return void
     * @throws \Exception
     */
    public function forceDeleteModel(Request $request, Model $model): void
    {
        if (!$this->modelUsesSoftDeletesTrait($model)) {
            throw new \Exception('Model class not uses SoftDeletes trait');
        }

        if (!$model->forceDelete()) {
            throw new \Exception('Model not permanently deleted');
        }

        if ($this->withImage) {
            $this->deleteFile($model->image ?? null);
        }
    }

    protected function modelUsesSoftDeletesTrait(Model $model): bool
    {
        return in_array(SoftDeletes::class, class_uses($model));
    }

    /**
     * @param array $imageInput
     * @param array $options <table>
     *  <tr>    <th>key of option</th>        <th>Default</th>        </tr>
     *  <tr>    <td>model</td>     <td>model to process</td>     </tr>
     *  <tr>    <td>image_field_key</td>      <td>image</td>      </tr>
     *  <tr>    <td>old_path</td>     <td>old path for image</td>       </tr>
     *  <tr>    <td>is_remove_key</td>     <td>field name if remove existed image</td>       </tr>
     * </table>
     *
     * @return bool|string|null
     */
    public function handleOneImage(array $imageInput, array $options = [])
    {
        /** @var Model $model */
        $model = Arr::get($options, 'model', $this->model);
        $imageFieldKey = Arr::get($options, 'image_field_key', 'image');
        $isRemoveKey = Arr::get($options, 'is_remove_key', 'isRemoveImage');

        $options = [
            'folder' => Arr::get($options, 'folder', 'images'),
            'model' => $model,
            'old_path' => Arr::get($options, 'old_path', $model->getAttribute($imageFieldKey) ?? null),
        ];

        return $this->handleUploadedFile(
            Arr::get($imageInput, $imageFieldKey),
            boolval(Arr::get($imageInput, $isRemoveKey, false)),
            $options
        );
    }

    /**
     * @param UploadedFile|null $file
     * @param bool|null $shouldRemove
     * @param array $options <table>
     *  <tr>    <th>key of option</th>        <th>Default</th>        </tr>
     *  <tr>    <td>folder</td>     <td>files</td>     </tr>
     *  <tr>    <td>model</td>     <td>model to process</td>     </tr>
     *  <tr>    <td>module</td>     <td>table of model</td>       </tr>
     *  <tr>    <td>old_path</td>     <td>old path for image</td>       </tr>
     * </table>
     *
     * @return bool|string|null
     */
    public function handleUploadedFile(?UploadedFile $file = null, ?bool $shouldRemove = false, array $options = [])
    {
        $folder = Arr::get($options, 'folder', 'files');
        /** @var Model $model */
        $model = Arr::get($options, 'model', $this->model);
        $module = Arr::get($options, 'module', $this->model ? module_name_from_model($this->model) : null);
        $oldPath = Arr::get($options, 'old_path');

        if (isset($file) || $shouldRemove) {
            $uniqId = $model->getKey() ?? null;

            return $this->storeUploadedFile($file, (string)$uniqId, $oldPath, $folder, $module);
        }

        return false;
    }

    /**
     * @param UploadedFile|mixed|null $image
     * @param string|null $uniqId
     * @param string|null $oldPath
     * @param string|null $type
     * @param string|null $module
     *
     * @return string|null
     * @deprecated `saveImage` will be removed is future releases, use `storeUploadedFile` instead
     */
    public function saveImage($image, string $uniqId = null, string $oldPath = null, string $type = null, string $module = null): ?string
    {
        return $this->storeUploadedFile($image, $uniqId, $oldPath, $type, $module);
    }

    /** @deprecated `deleteImage` will be removed is future releases, use `deleteFile` instead */
    public function deleteImage(?string $path): bool
    {
        return $this->deleteFile($path);
    }

    /**
     * @param UploadedFile|mixed|null $file
     * @param string|null $uniqId
     * @param string|null $oldPath
     * @param string|null $type
     * @param string|null $module
     *
     * @return string|null
     */
    protected function storeUploadedFile($file, string $uniqId = null, string $oldPath = null, string $type = null, string $module = null): ?string
    {
        $this->deleteFile($oldPath);

        if (empty($module)) {
            $module = $this->model ? module_name_from_model($this->model) : 'uploads';
        }

        $type = $type ?: 'file';

        return FileUploader::put($file, $type, $module, $uniqId) ?? null;
    }

    protected function deleteFile(?string $path, string $disk = 'public'): bool
    {
        if (empty($path)) {
            return false;
        }

        return Storage::disk($disk)->delete($path);
    }
}
