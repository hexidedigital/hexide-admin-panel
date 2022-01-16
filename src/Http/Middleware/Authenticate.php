<?php

namespace HexideDigital\HexideAdmin\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (in_array('admin', $guards)) {
            if (!(Auth::check() && Auth::user()->hasAdminAccess())) {
                Auth::logout();

                return redirect(route('admin.login'));
            }

            return $next($request);
        }

        return parent::handle($request, $next, ...$guards);
    }
}
