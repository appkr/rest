<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\SocialRedirectContract;
use App\Http\Controllers\Controller;
use App\Repositories\SocialRepository;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Socialite;
use App\User;

/**
 * Class SocialController
 */
class SocialController extends Controller implements SocialRedirectContract
{
    /**
     * @var Guard
     */
    protected $auth;

    /**
     * @var SocialRepository
     */
    protected $repo;

    /**
     * @var Request
     */
    protected $request;

    /**
     * OauthController
     *
     * @param Guard            $auth
     * @param SocialRepository $repo
     * @param Request          $request
     */
    public function __construct(Guard $auth, SocialRepository $repo, Request $request)
    {
        $this->auth    = $auth;
        $this->repo    = $repo;
        $this->request = $request;

        parent::__construct();
    }

    /**
     * Handle social login process
     *
     * @param string $provider
     *
     * @return mixed
     */
    public function execute($provider)
    {
        if (! $this->request->has('code')) {
            return $this->redirectToProvider($provider);
        }

        return $this->handleProviderCallback($provider);
    }

    /**
     * {@inheritDoc}
     */
    public function onSocialLoginSuccess(User $user)
    {
        $this->auth->login($user, true);

        event('UserHasLoggedIn', [$user]);

        return ($return = $this->request->input('return'))
            ? redirect()->route('return')
            : redirect()->intended(route('home'));
    }

    /**
     * Redirecting the user to the OAuth provider
     *
     * @param string $provider
     *
     * @return mixed
     */
    protected function redirectToProvider($provider)
    {
        return Socialite::with($provider)->redirect();
    }

    /**
     * Receiving the callback from the provider after authentication
     *
     * @param string $provider
     *
     * @return mixed
     * @throws \Exception
     */
    protected function handleProviderCallback($provider)
    {
        $user = Socialite::with($provider)->user();

        return $this->repo->firstOrCreate([
            'provider'    => $provider,
            'provider_id' => $user->getId(),
            'name'        => $user->getName(),
            'nickname'    => $user->getNickname(),
            'email'       => $user->getEmail(),
            'avatar'      => $user->getAvatar()
        ], $this);
    }
}
