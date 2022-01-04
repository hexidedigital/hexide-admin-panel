<?php

namespace HexideDigital\HexideAdmin\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (in_array('admin', $guards)) {
            /** @var User|null $user */
            $user = Auth::user();

            if (isset($user) && ($user->hasAdminAccess() || $user->roles()->pluck('key')->contains('admin'))) {
                $user->load(['roles', 'roles.permissions']);

                return $next($request);
            }

            Auth::logout();

            return redirect(route('admin.login'));
        }

        return parent::handle($request, $next, ...$guards);
    }
}
