<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class SessionControllerTest extends MailTestCase
{
    use DatabaseTransactions;
    use AuthTestTrait;

    /**
     * @var array Not yet encrypted credentials
     */
    private $credentials = [
        'email'    => 'john@example.com',
        'password' => 'password'
    ];

    /** @test */
    public function it_logs_a_user_in()
    {
        $user = $this->getAnAccount($this->getCredentials());

        $this->logIn($this->credentials)
            ->see(trans('auth.successLogin', ['name' => $user->name]));
    }

    /** @test */
    public function it_notifies_user_that_he_needs_activation()
    {
        $user = $this->getAnAccount(array_merge(
            $this->getCredentials(),
            ['activated' => 0]
        ));

        $this->logIn($this->credentials)
            ->see(trans('auth.notActivated', [
                'link' => route('user.send-activation', $user->activation_code)
            ]))
            ->onPage(route('session.create'));

        $this->assertHasOldInput();
    }

    /** @test */
    public function it_should_not_proceed_when_validation_fails()
    {
        $this->logIn(['email' => 'abc', 'password' => ''])
            ->see(trans('validation.email', ['attribute' => 'email']))
            ->see(trans('validation.required', ['attribute' => 'password']))
            ->onPage(route('session.create'));

        $this->assertHasOldInput();
    }

    /** @test */
    public function it_should_fails_login_when_credentials_not_match()
    {
        $this->logIn($this->getCredentials())
            ->see(trans('auth.failedLogin'))
            ->onPage(route('session.create'));

        $this->assertHasOldInput();
    }

    /** @test */
    public function it_logs_a_user_out()
    {
        $user = $this->getAnAccount($this->getCredentials());

        $this->actingAs($user)
            ->logOut()
            ->seePageIs(route('front'));
    }

    /**
     * @return array
     */
    private function getCredentials()
    {
        return [
            'email'    => $this->credentials['email'],
            'password' => bcrypt($this->credentials['password'])
        ];
    }
}