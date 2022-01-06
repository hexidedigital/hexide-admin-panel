<?php

namespace HexideDigital\HexideAdmin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminSecretRouteMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() || empty(config('hexide-admin.secret_key')) || $request->has(config('hexide-admin.secret_key'))){
            return $next($request);
        }

        return redirect('/');
    }
}
