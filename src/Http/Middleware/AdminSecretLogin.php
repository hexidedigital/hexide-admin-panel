<?php

namespace HexideDigital\HexideAdmin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminSecretLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() || $request->has(config('hexide_admin.secret_key')) || empty(config('hexide_admin.secret_key'))){
            return $next($request);
        }

        return redirect('/');
    }
}
