<?php

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
            $this->deleteImage($model->image ?? null);
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
            $this->deleteImage($model->image ?? null);
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
     *  <tr>    <td>image_field_key</td>      <td>image</td>      </tr>
     *  <tr>    <td>folder</td>     <td>images</td>     </tr>
     *  <tr>    <td>module</td>     <td>table of model</td>       </tr>
     *  <tr>    <td>old_path</td>     <td>old path for image</td>       </tr>
     *  <tr>    <td>is_remove_key</td>     <td>field name if remove existed image</td>       </tr>
     * </table>
     *
     * @return bool|string|null
     */
    public function handleOneImage(array $imageInput, array $options = [])
    {
        $fieldKey = Arr::get($options, 'image_field_key', 'image');
        $folder = Arr::get($options, 'folder', 'images');
        $module = Arr::get($options, 'module', $this->model->getTable());
        $oldPath = Arr::get($options, 'old_path', $this->model->{$fieldKey} ?? null);
        $isRemove = Arr::get($options, 'is_remove_key', 'isRemoveImage');

        $image = Arr::get($imageInput, $fieldKey);

        if (
            (isset($image) && $image instanceof UploadedFile)
            || Arr::get($imageInput, $isRemove, false)
        ) {
            return $this->saveImage(
                $image,
                $this->model->getKey() ?? null,
                $oldPath,
                $folder,
                $module ?? null,
            );
        }

        return false;
    }

    /**
     * @param UploadedFile|null $file
     * @param bool|null $isRemove
     * @param array $options <table>
     *  <tr>    <th>key of option</th>        <th>Default</th>        </tr>
     *  <tr>    <td>folder</td>     <td>images</td>     </tr>
     *  <tr>    <td>module</td>     <td>table of model</td>       </tr>
     *  <tr>    <td>old_path</td>     <td>old path for image</td>       </tr>
     * </table>
     *
     * @return bool|string|null
     */
    public function handleUploadedFile(?UploadedFile $file = null, ?bool $isRemove = false, array $options = [])
    {
        $folder = Arr::get($options, 'folder', 'images');
        $module = Arr::get($options, 'module', $this->model ? $this->model->getTable() : null);
        $oldPath = Arr::get($options, 'old_path');

        if (isset($file) || $isRemove) {
            $uniqId = $this->model->getKey() ?? null;
            return $this->saveImage($file, $uniqId, $oldPath, $folder, $module);
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
     */
    public function saveImage($image, string $uniqId = null, string $oldPath = null, string $type = null, string $module = null): ?string
    {
        if (!empty($oldPath)) {
            $this->deleteImage($oldPath);
        }

        if (empty($module)) {
            $module = $this->model->getTable() ?? null;
        }

        if (empty($type)) {
            $type = 'images';
        }

        return FileUploader::put($image, $type, $module, $uniqId) ?? null;
    }

    public function deleteImage(?string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }
}
