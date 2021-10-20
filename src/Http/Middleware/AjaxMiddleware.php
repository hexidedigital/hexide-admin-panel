<?php

namespace HexideDigital\HexideAdmin\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

/**
 * Class AjaxMiddleware
 * @package App\Http\Middleware
 */
class AjaxMiddleware
{

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth
     *
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            if (is_admin_panel()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['status' => 'error'], 403);
                }

                return redirect()->back();
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error'], 403);
            }

            return redirect()->route('home');
        }

        return $next($request);
    }
}
