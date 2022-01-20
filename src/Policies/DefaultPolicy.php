<?php

namespace HexideDigital\HexideAdmin\Policies;

use App\Models\User;
use HexideDigital\ModelPermissions\Models\Permission as HDPermission;
use Illuminate\Database\Eloquent\Model;

abstract class DefaultPolicy
{
    public function before(User $user, $ability): ?bool
    {
        if ($user->isRoleSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionKey(HDPermission::ViewAny, $this->module())
            && $user->hasAdminAccess();
    }

    public function view(User $user, Model $model): bool
    {
        return $user->hasPermissionKey(HDPermission::View, $this->module())
            && $user->hasAdminAccess();
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionKey(HDPermission::Create, $this->module())
            && $user->hasAdminAccess();
    }

    public function update(User $user, Model $model): bool
    {
        return $user->hasPermissionKey(HDPermission::Update, $this->module())
            && $user->hasAdminAccess();
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->hasPermissionKey(HDPermission::Delete, $this->module())
            && $user->hasAdminAccess();
    }

    public function restore(User $user, Model $model): bool
    {
        return $user->hasPermissionKey(HDPermission::Delete, $this->module())
            && $user->hasAdminAccess();
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $user->hasPermissionKey(HDPermission::Delete, $this->module())
            && $user->hasAdminAccess();
    }

    public function ajax(User $user, Model $model): bool
    {
        return $user->hasPermissionKey('ajax', $this->module())
            && $user->hasAdminAccess();
    }

    abstract protected function module(): string;
}
