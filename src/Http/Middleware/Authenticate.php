<?php

namespace HexideDigital\HexideAdmin\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (!in_array('admin', $guards)) {
            return parent::handle($request, $next, ...$guards);
        }

        if (Auth::check() && Auth::user()->hasAdminAccess()) {
            return $next($request);
        }

        Auth::logout();

        session()->put('redirect_uri', $request->getRequestUri());

        return redirect(route('admin.login'));
    }
}
