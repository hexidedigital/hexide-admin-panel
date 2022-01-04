<?php

namespace HexideDigital\HexideAdmin\Services;

use HexideDigital\FileUploader\Facades\FileUploader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class BackendService implements ServiceInterface
{
    /**
     * @param FormRequest|Request $request
     * @param Model $model
     * @return Model
     */
    public function handleRequest(Request $request, Model $model): Model
    {
        $attributes = $request instanceof FormRequest
            ? $request->validated()
            : $request->all();

        $this->prepareAttributes($attributes, $model);

        $model = $this->saveModel($attributes, $model);

        $this->postHandle($request, $model);

        return $model;
    }

    public function saveModel(array $attributes, Model $model): Model
    {
        $model->fill($attributes);

        $model->save();

        return $model->refresh();
    }

    public function prepareAttributes(array &$inputs, Model $model): void
    {
        $inputs['status'] = $inputs['status'] ?? false;
    }

    public function postHandle(Request $request, Model $model)
    {

    }

    /**
     * @param Request $request
     * @param Model|null $model
     * @param array $options <table>
     *  <tr>    <th>key of option</th>        <th>Default</th>        </tr>
     *  <tr>    <td>field_key</td>      <td>image</td>      </tr>
     *  <tr>    <td>folder</td>     <td>images</td>     </tr>
     *  <tr>    <td>module</td>     <td>NULL</td>       </tr>
     * </table>
     *
     * @return false|string|null
     */
    public function handleOneImage(Request $request, Model $model, array $options = [])
    {
        $path = false;

        $options['field_key'] = Arr::get($options, 'field_key', 'image');
        $options['folder'] = Arr::get($options, 'folder', 'images');
        $options['module'] = Arr::get($options, 'module', $model->getTable());

        $old_path = $model->{$options['field_key']} ?? null;

        if ($request->hasFile('image') || $request->input('isRemoveImage', false)) {
            $path = $this->saveImage(
                $request->file('image'),
                $request->input('slug'),
                $old_path,
                $options['folder'],
                $options['module'] ?? null,
            );
        }

        return $path;
    }

    /**
     * @param UploadedFile|mixed|null $image
     * @param string|null $uniq_id to place in the same folder
     * @param string|null $old_path to delete old photo
     * @param string|null $type default is `images`
     * @param string|null $module
     * @return string|null
     */
    public function saveImage($image,
                              string $uniq_id = null,
                              string $old_path = null,
                              string $type = null,
                              string $module = null): ?string
    {
        if (!empty($old_path)) {
            Storage::disk('public')->delete($old_path);
        }

        if (empty($module)) {
            $module = $this->module ?? null;
        }

        if (empty($type)) {
            $type = 'images';
        }

        return FileUploader::put($image, $type, $module, $uniq_id) ?? null;
    }
}
