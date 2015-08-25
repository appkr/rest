<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string                   $role
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next, $role)
    {
        // Here we utilize L5 middleware parameter feature
        // $router->put('path', ['middleware' => 'role:root|admin', ...]);
        if (! $request->user()->is($role)) {
            if ($request->ajax() or $request->is(config('fractal.pattern'))) {
                throw new \Exception(strip_tags(trans('auth.notAuthorized')), 400);
            } else {
                flash()->error(trans('auth.notAuthorized'));

                return redirect()->back();
            }
        }

        return $next($request);
    }
}
