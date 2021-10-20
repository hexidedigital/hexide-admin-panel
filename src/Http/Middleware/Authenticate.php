<?php

namespace HexideDigital\HexideAdmin\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param mixed ...$guards
     * @return mixed
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if(in_array('admin', $guards)){
            /** @var User|null $user */
            $user = Auth::user();

            if(isset($user) && ($user->hasAdminAccess() || $user->roles()->pluck('key')->contains('admin'))){
                $request->merge(['auth_user' => $user]);
                return $next($request);
            }

            Auth::logout();

            return redirect(route('admin.login'));
        }

        return parent::handle($request, $next, ...$guards);
    }

}