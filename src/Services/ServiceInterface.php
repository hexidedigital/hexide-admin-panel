<?php

namespace HexideDigital\HexideAdmin\Services;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface ServiceInterface
{
    /**
     * @param Request $request
     * @return Model|Builder
     */
    public function createModel(Request $request);

    /**
     * @param Request $request
     * @param Model $model
     * @return Model|Builder|null
     */
    public function updateModel(Request $request, Model $model);

    /**
     * @param Request $request
     * @param Model|null $model
     * @return Model|mixed
     */
    public function handleRequest(Request $request, ?Model $model = null);

}
