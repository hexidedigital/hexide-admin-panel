<?php

namespace HexideDigital\HexideAdmin\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class AjaxMiddleware
{
    /** The Guard implementation. */
    protected Guard $auth;

    /** Create a new filter instance. */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
