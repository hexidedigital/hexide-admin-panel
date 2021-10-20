<?php

namespace HexideDigital\HexideAdmin\Http\Middleware;

use App\Models\User;
use Closure;
use HexideDigital\HexideAdmin\Models\Permission;
use HexideDigital\HexideAdmin\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthGates
{
    public function handle($request, Closure $next)
    {
        /**
         * @var User $user
         * @var Role $role
         * @var Permission $permissions
         */

        $user = Auth::user();

        if (!app()->runningInConsole() && $user) {
            $roles = Role::with('permissions')->get();

            foreach ($roles as $role) {
                foreach ($role->permissions as $permissions) {
                    $permissionsArray[$permissions->title][] = $role->id;
                }
            }

            if(empty($permissionsArray) == true){
                Auth::logout();
                return redirect(route('admin.login'));
            }else {

                foreach ($permissionsArray as $title => $role_id) {
                    Gate::define($title, function (User $user) use ($role_id) {
                        return count(array_intersect($user->roles->pluck('id')->toArray(), $role_id)) > 0;
                    });
                }
            }
        }

        return $next($request);
    }
}
