<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Repositories\UserRepository;
use App\Social;
use Bican\Roles\Models\Role;
use Illuminate\Contracts\Auth\Guard;
use App\User;

/**
 * Class RegisterController
 */
class RegisterController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $repo;

    /**
     * The Guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * Create a new authentication controller instance.
     *
     * @param UserRepository $repo
     * @param Guard          $auth
     */
    public function __construct(UserRepository $repo, Guard $auth)
    {
        $this->repo = $repo;
        $this->auth = $auth;

        if (! is_api_request()) {
            $this->middleware('auth', ['only' => ['destroy']]);
            $this->middleware('guest', ['except' => ['destroy']]);
        } else {
            $this->middleware('jwt.auth', ['only' => ['destroy']]);
            $this->middleware('jwt.refresh', ['only' => ['destroy']]);
        }

        parent::__construct();
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param AuthRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(AuthRequest $request)
    {
        $activationCode = str_random(60);

        $user = $this->createNewUser(array_merge(
            $request->all(),
            [
                'password'        => bcrypt($request->input('password')),
                'activation_code' => $activationCode
            ]
        ));

        event('NewUserRegistered', [$user]);

        return $this->respondRegistered($user);
    }

    /**
     * Activate user
     *
     * @param  string $code
     * @return \Illuminate\Http\Response
     */
    public function activate($code)
    {
        $user = $this->repo->findByActivationCode($code);

        if ($user) {
            $this->auth->login($user);

            event('UserActivated', [$user]);

            return $this->respondActivated($user);
        }

        return $this->respondActivationError();
    }

    /**
     * Send activation email
     *
     * @param $code
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function sendActivation($code)
    {
        $user = $this->repo->findByActivationCode($code);

        event('NewUserRegistered', [$user]);

        return $this->respondRecommendActivation();
    }

    /**
     * Handle delete account request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $user = $this->auth->getUser();

        $this->repo->destroy($user->id);

        event('UserDeleted', [$user]);

        return $this->respondDeleteSuccess();
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function createNewUser(array $data)
    {
        $user = $this->repo->create($data);

        return $user;
    }

    /**
     * Make a response for the case when the registration was successful
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function respondRegistered(User $user)
    {
        flash(trans('auth.recommendActivation'));

        return redirect(route('user.create'));
    }

    /**
     * Make a response for the case when the user was activated
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function respondActivated(User $user)
    {
        flash(trans('auth.activated'));

        return redirect()->home();
    }

    /**
     * Make a response for the case
     * when there was an error while attempting activating the user
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function respondActivationError()
    {
        flash()->error(trans('auth.activationError'))->important();

        return redirect(route('session.create'));
    }

    /**
     * Make a response recommending activation
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function respondRecommendActivation()
    {
        flash(trans('auth.recommendActivation'));

        return redirect(route('session.create'));
    }

    /**
     * Make a response for the case when the delete user succeeded
     *
     * @return mixed
     */
    protected function respondDeleteSuccess()
    {
        flash(trans('auth.successCancel'));

        $this->auth->logout();

        return redirect(route('front'));
    }
}
