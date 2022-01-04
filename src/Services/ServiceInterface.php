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
     * @return Model
     */
    public function handleRequest(Request $request, Model $model): Model;

    public function saveModel(array $attributes, Model $model): Model;

    public function prepareAttributes(array &$inputs, Model $model): void;

    public function postHandle(Request $request, Model $model);
}
