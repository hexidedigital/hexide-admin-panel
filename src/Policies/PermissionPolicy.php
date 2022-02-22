<?php

namespace HexideDigital\HexideAdmin\Policies;

use App\Models\User;
use Str;
use HexideDigital\ModelPermissions\Models\Permission;

class PermissionPolicy extends DefaultPolicy
{
    /**
     * @param User $user
     * @param Permission $model
     *
     * @return bool
     */
    public function update(User $user, $model): bool
    {
        return parent::update($user, $model)
            && !Str::startsWith($model->getOriginal('title'), config('model-permissions.startup_permissions', []));
    }

    /**
     * @param User $user
     * @param Permission $model
     *
     * @return bool
     */
    public function delete(User $user, $model): bool
    {
        return parent::Delete($user, $model)
            && !Str::startsWith($model->getOriginal('title'), config('model-permissions.startup_permissions', []));
    }

    protected function module(): string
    {
        return module_name_from_model(new Permission);
    }
}
