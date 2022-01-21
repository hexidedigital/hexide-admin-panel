<?php

namespace HexideDigital\HexideAdmin\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

interface ServiceInterface
{
    /**
     * @param FormRequest|Request $request
     * @param Model $model
     *
     * @return Model
     */
    public function handleRequest(Request $request, Model $model): Model;

    /**
     * @param FormRequest|Request $request
     * @param Model $model
     *
     * @return array
     */
    public function processInputData(Request $request, Model $model): array;

    /**
     * @param array $attributes
     * @param Model $model
     *
     * @return Model
     */
    public function saveModel(array $attributes, Model $model): Model;

    /**
     * @param FormRequest|Request $request
     * @param Model $model
     *
     * @return Model
     */
    public function postHandle(Request $request, Model $model): Model;

    /**
     * @param FormRequest|Request $request
     * @param Model $model
     *
     * @return void
     */
    public function deleteModel(Request $request, Model $model): void;

}
