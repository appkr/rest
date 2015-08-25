<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Auth\SocialController as GlobalSocialController;
use App\User;
use Appkr\Fractal\ApiResponse;
use Socialite;

class SocialController extends GlobalSocialController
{
    use ApiResponse;
    use AuthHelper;

    /**
     * {@inheritDoc}
     */
    public function execute($provider)
    {
        // Suffix 2 to properly pull config from services.json
        $provider = sprintf("%s2", $provider);

        return parent::execute($provider);
    }

    /**
     * Deliberate overriding to avoid responding html
     * from GlobalSocialController
     *
     * {@inheritDoc}
     */
    public function onSocialLoginSuccess(User $user)
    {
        $this->auth->login($user, true);

        event('UserHasLoggedIn', [$user]);

        return $this->respondLoginSuccess($user);
    }

    /**
     * Deliberate overriding to avoid pass
     * $this context to the SocialRepository
     *
     * {@inheritDoc}
     */
    protected function handleProviderCallback($provider)
    {
        $user = Socialite::with($provider)->user();

        return $user = $this->repo->firstOrCreate([
            // We strip off suffix. e.g. github2 -> github
            'provider'    => rtrim($provider, 2),
            'provider_id' => $user->getId(),
            'name'        => $user->getName(),
            'nickname'    => $user->getNickname(),
            'email'       => $user->getEmail(),
            'avatar'      => $user->getAvatar()
        ], $this);
    }

    /**
     * Make a response for the case when login was successful
     *
     * @param User $user
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function respondLoginSuccess(User $user)
    {
        $meta = $this->buildUserMeta($user);

        return $this->response()->setMeta($meta)
            ->success(strip_tags(session('flash_notification.message')));
    }
}

