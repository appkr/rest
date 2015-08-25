<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PasswordController extends Controller
{

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * The password broker implementation.
     *
     * @var PasswordBroker
     */
    protected $passwords;

    /**
     * Create a new password controller instance.
     *
     * @param Guard           $auth
     * @param  PasswordBroker $passwords
     */
    public function __construct(Guard $auth, PasswordBroker $passwords)
    {
        $this->auth      = $auth;
        $this->passwords = $passwords;

        $this->middleware('guest');

        parent::__construct();
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return Response
     */
    public function getReminder()
    {
        return view('auth.password');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function postReminder(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        // Notify user if he/she is a Social login user.
        // who does not have password
        $user = \App\User::whereEmail($request->only('email'))->first();

        if ($user && $user->isSocialUser()) {
            flash(trans('auth.noPasswordUser', [
                'socials' => implode(', ', $user->socials->lists('provider')->toArray())
            ]));

            return redirect()->back();
        }

        $response = $this->passwords->sendResetLink($request->only('email'), function ($m) {
            $m->subject($this->getEmailSubject());
        });

        switch ($response) {
            case PasswordBroker::RESET_LINK_SENT:
                flash(trans($response));

                return redirect()->back();

            case PasswordBroker::INVALID_USER:
                flash()->error(trans($response));

                return redirect()->back()->withInput();
        }
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string $token
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function getReset($token = null)
    {
        if (is_null($token)) {
            throw new NotFoundHttpException;
        }

        return view('auth.reset')->with('token', $token);
    }

    /**
     * Reset the given user's password.
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function postReset(Request $request)
    {
        $this->validate($request, [
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed',
        ]);

        $credentials = $request->only(
            'email',
            'password',
            'password_confirmation',
            'token'
        );

        $response = $this->passwords->reset($credentials, function ($user, $password) {
            $user->password = bcrypt($password);
            $user->save();

            if ($user->activated != 1) {
                return $this->respondNotActivated($user->code);
            }

            $this->auth->login($user);
        });

        switch ($response) {
            case PasswordBroker::PASSWORD_RESET:
                flash(trans('auth.successReset'));

                return redirect(route('home'));
            //return redirect(trans($response));

            default:
                return redirect()->back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => trans($response)]);
        }
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (property_exists($this, 'redirectPath')) {
            return $this->redirectPath;
        }

        return property_exists($this, 'redirectTo')
            ? $this->redirectTo
            : route('home');
    }

    /**
     * Get the e-mail subject line to be used for the reset link email.
     *
     * @return string
     */
    protected function getEmailSubject()
    {
        return isset($this->subject)
            ? $this->subject
            : trans('passwords.resetSubject');
    }

    /**
     * Make a response for the case when the user is not activated
     *
     * @param string      $code
     *
     * @return $this
     */
    protected function respondNotActivated($code)
    {
        flash()->error(trans('auth.notActivated', [
            'link' => route('user.send-activation', $code)
        ]))->important();

        return redirect(route('session.create'));
    }

}
