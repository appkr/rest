<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Auth\SessionController as GlobalSessionController;
use App\Http\Requests\AuthRequest;
use Appkr\Api\Http\ApiResponse;
use App\User;

/**
 * Class SessionController
 */
class SessionController extends GlobalSessionController
{
    use ApiResponse;
    use AuthHelper;

    /**
     * {@inheritDoc}
     */
    protected function respondNotActivated(AuthRequest $request, $code)
    {
        if (! $code) {
            return $this->response()->internalError('Activation code was not passed');
        }

        $payload = trans('auth.notActivated', [
            'link' => route('api.user.send-activation', $code)
        ]);

        return $this->response()->setMeta([
            'link' => route('api.user.send-activation', $code)
        ])->forbiddenError(strip_tags($payload));
    }

    /**
     * {@inheritDoc}
     */
    protected function respondLoginSuccess(AuthRequest $request, User $user)
    {
        $meta = $this->buildUserMeta($user);

        return $this->response()->setMeta($meta)
            ->success(strip_tags(trans('auth.successLogin', ['name' => $user->name])));
    }

    /**
     * {@inheritDoc}
     */
    protected function respondLoginFail(AuthRequest $request)
    {
        return $this->response()->forbiddenError(strip_tags(trans('auth.failedLogin')));
    }

    /**
     * {@inheritDoc}
     */
    protected function respondLogout()
    {
        // Don't need to invalidate the token
        // since it's complete stateless (no server session)
        //$token = JWTAuth::getToken();
        //JWTAuth::invalidate($token);

        return $this->response()->success(strip_tags(trans('auth.logout')));
    }
}
