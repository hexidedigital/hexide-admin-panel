<?php

namespace HexideDigital\HexideAdmin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSecretRouteMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()
            || empty(config('hexide-admin.configurations.secret_key'))
            || $request->has(config('hexide-admin.configurations.secret_key'))
        ){
            return $next($request);
        }

        return redirect('/');
    }
}
