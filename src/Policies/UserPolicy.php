<?php

namespace HexideDigital\HexideAdmin\Policies;

use App\Models\User;

class UserPolicy extends DefaultPolicy
{
    /**
     * @param User $user
     * @param User $model
     *
     * @return bool
     */
    public function update(User $user, $model): bool
    {
        return parent::update($user, $model)

            && ($model->id === $user->id || $user->isRoleSuperAdmin());
    }

    /**
     * @param User $user
     * @param User $model
     *
     * @return bool
     */
    public function delete(User $user, $model): bool
    {
        return parent::delete($user, $model)

            && $model->id !== $user->id
            && !$model->is_system
            && !$model->isRoleSuperAdmin();
    }

    /**
     * @param User $user
     * @param User $model
     *
     * @return bool
     */
    public function forceDelete(User $user, $model): bool
    {
        return parent::forceDelete($user, $model)

            && $model->id !== $user->id
            && !$model->is_system
            && !$model->isRoleSuperAdmin();
    }

    protected function module(): string
    {
        return module_name_from_model(new User);
    }
}
