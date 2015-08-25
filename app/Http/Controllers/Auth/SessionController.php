<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;

/**
 * Class SessionController
 */
class SessionController extends Controller
{
    use ThrottlesLogins;

    /**
     * @var UserRepository
     */
    private $repo;

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
            $this->middleware('guest', ['except' => ['getLogout']]);
        }

        parent::__construct();
    }

    /**
     * Show the application login form.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getLogin(Request $request)
    {
        return ($return = $request->input('return'))
            ? view('auth.login')->withReturn($return)
            : view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param AuthRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(AuthRequest $request)
    {
        $throttles = in_array(
            ThrottlesLogins::class, class_uses_recursive(get_class($this))
        );

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->respondThrottled($request);
        }

        if (! $this->auth->once($request->only('email', 'password'))) {
            if ($throttles) {
                $this->incrementLoginAttempts($request);
            }

            return $this->respondLoginFail($request);
        }

        $user = $this->auth->getUser();

        if (! $user->activated) {
            $this->auth->logout();

            return $this->respondNotActivated($request, $user->activation_code);
        }

        $this->auth->loginUsingId($user->id, $request->has('remember'));

        if ($throttles) {
            $this->clearLoginAttempts($request);
        }

        event('UserHasLoggedIn', [$this->auth->user()]);

        return $this->respondLoginSuccess($request, $user);
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout()
    {
        $this->auth->logout();

        event('UserHasLoggedOut');

        return $this->respondLogout();
    }

    /**
     * Get the path to the login route.
     *
     * @return string
     */
    public function loginPath()
    {
        return property_exists($this, 'loginPath') ? $this->loginPath : '/auth/login';
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function loginUsername()
    {
        return property_exists($this, 'username') ? $this->username : 'email';
    }

    /**
     * Make a response for the case when the user is not activated
     *
     * @param AuthRequest $request
     * @param string      $code
     * @return $this
     */
    protected function respondNotActivated(AuthRequest $request, $code)
    {
        flash()->error(trans('auth.notActivated', [
            'link' => route('user.send-activation', $code)
        ]))->important();

        return redirect(route('session.create'))
            ->withInput($request->only('email', 'remember'));
    }

    /**
     * Make a response for the case when login was successful
     *
     * @param AuthRequest $request
     * @param User        $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function respondLoginSuccess(AuthRequest $request, User $user)
    {
        flash(trans('auth.successLogin', ['name' => $user->name]));

        return ($return = $request->input('return'))
            ? redirect(route($return))
            : redirect()->intended(route('home'));
    }

    /**
     * Make a response for the case when the login fails
     *
     * @param AuthRequest $request
     * @return $this
     */
    protected function respondLoginFail(AuthRequest $request)
    {
        flash()->error(trans('auth.failedLogin'));

        return redirect(route('session.create'))
            ->withInput($request->only('email', 'remember'));
    }

    /**
     * Make a response for the case when logout was successful
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function respondLogout()
    {
        flash(trans('auth.logout'));

        return redirect(route('front'));
    }

    /**
     * Make a too many login attempt error response
     *
     * @param $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function respondThrottled($request)
    {
        flash()->error(trans('passwords.throttled'));

        return $this->sendLockoutResponse($request);
    }
}
