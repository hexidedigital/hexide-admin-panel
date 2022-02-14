<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Services\Backend;

use App\Models\User;
use HexideDigital\HexideAdmin\Services\BackendService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UserService extends BackendService
{
    /**
     * @param FormRequest $request
     * @param User $model
     *
     * @return array
     */
    public function processInputData(Request $request, Model $model): array
    {
        $inputs = $request->validated();

        if (empty($inputs['password'])) {
            unset($inputs['password']);
        } else {
            $inputs['password'] = bcrypt($inputs['password']);
        }

        return $inputs;
    }

    /**
     * @param Request $request
     * @param User $model
     *
     * @return Model|User
     */
    public function postHandle(Request $request, Model $model): Model
    {
        if ($request->has('roles')) {
            $model->roles()->sync($request->input('roles', []));
        }

        return $model;
    }
}
