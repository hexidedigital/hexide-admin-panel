<?php

namespace HexideDigital\HexideAdmin\Http\Middleware;

use App\Models\User;
use Closure;
use HexideDigital\ModelPermissions\Models\Permission;
use HexideDigital\ModelPermissions\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthGates
{
    public function handle($request, Closure $next)
    {
        if (!app()->runningInConsole() && Auth::check()) {
            /** @var Collection|Role[] $roles */
            $roles = Role::with('permissions')->get();
            $permissionsArray = [];

            foreach ($roles as $role) {
                /** @var Permission $permissions */
                foreach ($role->permissions as $permissions) {
                    $permissionsArray[$permissions->title][] = $role->id;
                }
            }

            foreach ($permissionsArray as $title => $roles_ids) {
                Gate::define($title, function (User $user) use ($roles_ids) {
                    return count(array_intersect($user->roles->pluck('id')->toArray(), $roles_ids)) > 0;
                });
            }
        }

        return $next($request);
    }
}
