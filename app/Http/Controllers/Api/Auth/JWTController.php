<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;

class JWTController extends Controller
{
    /**
     * Issues new token
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Http\Response
     */
    public function refresh(Request $request)
    {
        $newToken = JWTAuth::setRequest($request)->parseToken()->refresh();

        return $this->response()->setMeta([
            'token' => $newToken
        ])->success(strip_tags(trans('auth.tokenRefreshed')));
    }
}
