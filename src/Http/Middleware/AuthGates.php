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

            foreach ($roles as $role) {
                /** @var Permission $permissions */
                foreach ($role->permissions as $permissions) {
                    $permissionsArray[$permissions->title][] = $role->id;
                }
            }

            if (empty($permissionsArray) == true) {
                Auth::logout();
                return redirect(route('admin.login'));
            }

            foreach ($permissionsArray as $title => $role_id) {
                Gate::define($title, function (User $user) use ($role_id) {
                    return count(array_intersect($user->roles->pluck('id')->toArray(), $role_id)) > 0;
                });
            }
        }

        return $next($request);
    }
}
