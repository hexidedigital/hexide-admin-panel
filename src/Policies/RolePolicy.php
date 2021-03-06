<?php

namespace HexideDigital\HexideAdmin\Policies;

use App\Models\User;
use HexideDigital\ModelPermissions\Models\Role;

class RolePolicy extends DefaultPolicy
{
    protected function module(): string
    {
        return module_name_from_model(new Role);
    }

    /**
     * @param User $user
     * @param Role $model
     *
     * @return bool
     */
    public function delete(User $user, $model): bool
    {
        return parent::delete($user, $model)
            && !in_array($model->id, [Role::SuperAdmin, Role::Admin]);
    }
}
