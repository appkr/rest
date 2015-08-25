<?php

trait AuthTestTrait
{
    /**
     * Sign up a user
     *
     * @param array|null $overrides
     * @return $this
     */
    public function signUp($overrides = [])
    {
        $data = array_merge([
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password'
        ], $overrides);

        return $this->visit(route('user.create'))->submitForm('Register', $data);
    }

    /**
     * Activate user account
     *
     * @param string $email
     * @return $this
     */
    public function activateUser($email)
    {
        $this->signUp(['email' => $email]);
        $code = \App\User::whereEmail($email)->first()->activation_code;

        return $this->visit(route('user.activate', $code));
    }

    /**
     * Create a new account
     *
     * @param array|null $overrides
     * @return mixed
     */
    public function getAnAccount($overrides = [])
    {
        $user = factory(\App\User::class)->create($overrides);

        if (! isset($overrides['activated']) or $overrides['activated'] !== 0) {
            $user->activated    = 1;
            $user->activated_at = (new DateTime)->format('Y-m-d H:i:s');
            $user->save();
        }

        return $user->fresh();
    }

    /**
     * Log a user in
     *
     * @param array $credentials
     * @return mixed
     */
    public function logIn($credentials = [])
    {
        return $this->visit(route('session.create'))->submitForm('Login', $credentials);
    }

    /**
     * Log a user out
     *
     * @return mixed
     */
    public function logOut()
    {
        return $this->visit(route('session.destroy'));
    }

    /**
     * Send password reset link through email
     *
     * @param null $email
     * @return mixed
     */
    public function passwordRemind($email = null)
    {
        $user = $this->getAnAccount($this->getCredentials());

        return $this->visit(route('reminder.create'))
            ->submitForm('Send Password Reset Link', ['email' => $email ?: $user->email]);
    }

    /**
     * Do the password reset
     *
     * @param null  $token
     * @param array $overrides
     * @return mixed
     */
    public function passwordReset($token = null, $overrides = ['activated' => 0])
    {
        $user = $this->getAnAccount($this->getCredentials() + $overrides);
        $token = $token ?: $this->app['auth.password.tokens']->create($user);

        return $this->visit(route('reset.create', $token))
            ->submitForm('Reset Password', [
                'email' => $user->email,
                'password' => 'modified',
                'password_confirmation' => 'modified'
            ]);
    }
}