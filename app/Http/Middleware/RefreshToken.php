<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Middleware\BaseMiddleware;

class RefreshToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $newToken = $this->auth->setRequest($request)->parseToken()->refresh();

        return app('api.response')->response()->setMeta([
            'token' => $newToken
        ])->success(strip_tags(trans('auth.tokenRefreshed')));
    }
}
