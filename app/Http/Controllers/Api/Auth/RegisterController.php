<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Auth\RegisterController as GlobalRegisterController;
use App\User;
use Appkr\Fractal\ApiResponse;
use JWTAuth;

/**
 * Class RegisterController
 */
class RegisterController extends GlobalRegisterController
{
    use ApiResponse;
    use AuthHelper;

    /**
     * {@inheritDoc}
     */
    protected function respondRegistered(User $user)
    {
        $meta = $this->buildUserMeta($user);

        return $this->response()->setMeta($meta)
            ->created(strip_tags(trans('auth.recommendActivation')));
    }

    /**
     * {@inheritDoc}
     */
    protected function respondRecommendActivation()
    {
        return $this->response()->success(strip_tags(trans('auth.recommendActivation')));
    }

    /**
     * {@inheritDoc}
     */
    protected function respondDeleteSuccess()
    {
        return $this->response()->success(trans('auth.successCancel'));
    }
}
